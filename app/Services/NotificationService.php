<?php

namespace App\Services;

use App\Models\NotificationModel;
use App\Models\UserModel;
use App\Models\SptModel;
use App\Models\ArsipModel;
use App\Models\BeritaAcaraModel;
use App\Models\OpdModel;
use Config\Database;

class NotificationService
{
    protected $notifModel;
    protected $userModel;
    protected $sptModel;
    protected $arsipModel;
    protected $baModel;
    protected $opdModel;
    protected $db;

    public function __construct()
    {
        $this->notifModel = new NotificationModel();
        $this->userModel  = new UserModel();
        $this->sptModel   = new SptModel();
        $this->arsipModel = new ArsipModel();
        $this->baModel    = new BeritaAcaraModel();
        $this->opdModel   = new OpdModel();
        $this->db         = Database::connect();
    }

    /**
     * Kirim notifikasi tunggal ke pengguna tertentu
     * Dilengkapi pencegahan duplikasi dalam 5 menit terakhir untuk aktivitas yang sama
     */
    public function sendToUser(
        string $userId,
        string $title,
        string $message,
        string $type = 'general',
        ?string $referenceId = null,
        ?string $referenceType = null,
        ?string $url = null,
        ?string $senderId = null
    ): ?string {
        if (empty($userId) || empty($title) || empty($message)) {
            return null;
        }

        // Cek apakah notifikasi identik persis sudah pernah dikirim dalam 5 menit terakhir (cegah duplikasi)
        $builder = $this->db->table('notifications')
            ->where('id_user', $userId)
            ->where('title', $title)
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-5 minutes')));

        if (!empty($referenceId)) {
            $builder->where('reference_id', $referenceId);
        }

        $existing = $builder->get()->getRowArray();

        if ($existing) {
            return null; // Duplikasi dicegah: notifikasi identik telah dikirim dalam 5 menit terakhir
        }

        return $this->notifModel->send([
            'id_user'        => $userId,
            'sender_id'      => $senderId,
            'title'          => $title,
            'message'        => $message,
            'type'           => $type,
            'reference_id'   => $referenceId,
            'reference_type' => $referenceType ?: $type,
            'url'            => $url,
            'is_read'        => 0,
            'action_type'    => $type,
            'action_id'      => $referenceId
        ]);
    }

    // =========================================================================
    // 1. ALUR NOTIFIKASI OTOMATIS WORKFLOW SIAMA
    // =========================================================================

    /**
     * Skenario A: Pimpinan menerbitkan SPT
     * Penerima: Kepala Bidang yang berkaitan dengan OPD atau bidang pada SPT.
     * Pesan: "Surat Perintah Tugas baru telah diterbitkan. Silakan tindak lanjuti penugasan Arsiparis."
     * Aksi: Lihat Detail SPT.
     */
    public function notifySptCreated(string $id_spt, ?string $pimpinan_user_id = null): array
    {
        $spt = $this->sptModel->find($id_spt);
        if (!$spt) {
            return [];
        }

        $nomorSpt = $spt['nomor_spt'] ?? '-';
        $title = 'Surat Perintah Tugas Baru Diterbitkan';
        $message = "Surat Perintah Tugas baru telah diterbitkan. Silakan tindak lanjuti penugasan Arsiparis.";
        if (!empty($nomorSpt) && $nomorSpt !== '-') {
            $message = "Surat Perintah Tugas baru telah diterbitkan. Silakan tindak lanjuti penugasan Arsiparis. (Nomor: {$nomorSpt})";
        }
        $url = base_url('/spt/detail/' . $id_spt);

        // Cari Kepala Bidang di OPD bersangkutan
        $kabidQuery = $this->db->table('users u')
            ->join('user_roles ur', 'u.id_user = ur.id_user')
            ->join('roles r', 'ur.id_role = r.id_role')
            ->whereIn('r.nama_role', ['Kepala_Bidang', 'Kepala Bidang', 'Kabid'])
            ->where('u.is_active', 1);

        if (!empty($spt['id_opd'])) {
            $kabidQuery->where('u.id_opd', $spt['id_opd']);
        }

        $kabids = $kabidQuery->select('u.id_user')->get()->getResultArray();

        $sentIds = [];
        $uniqueRecipients = array_unique(array_column($kabids, 'id_user'));
        foreach ($uniqueRecipients as $kabidId) {
            $sent = $this->sendToUser(
                $kabidId,
                $title,
                $message,
                'spt',
                $id_spt,
                'spt',
                $url,
                $pimpinan_user_id
            );
            if ($sent) {
                $sentIds[] = $sent;
            }
        }

        return $sentIds;
    }

    /**
     * Skenario B: Kepala Bidang menugaskan Arsiparis
     * Penerima: Arsiparis yang dipilih dalam penugasan.
     * Pesan: "Anda mendapatkan penugasan alih media arsip baru."
     * Aksi: Lihat Penugasan.
     */
    public function notifySptAssigned(string $id_spt, string $id_user_arsiparis, ?string $assigned_by_id = null): ?string
    {
        $spt = $this->sptModel->find($id_spt);
        if (!$spt) {
            return null;
        }

        $nomorSpt = $spt['nomor_spt'] ?? '-';
        $title = 'Penugasan Alih Media Baru';
        $message = "Anda mendapatkan penugasan alih media arsip baru. Nomor SPT: {$nomorSpt}.";
        $url = base_url('/spt/detail/' . $id_spt);

        return $this->sendToUser(
            $id_user_arsiparis,
            $title,
            $message,
            'spt',
            $id_spt,
            'spt',
            $url,
            $assigned_by_id
        );
    }

    /**
     * Skenario C: Arsiparis memulai pekerjaan
     * Penerima: Kepala Bidang yang memberikan penugasan.
     * Pesan: "Proses alih media untuk SPT [Nomor SPT] telah dimulai."
     * Aksi: Pantau Pekerjaan.
     */
    public function notifySptStarted(string $id_spt, string $arsiparis_user_id): array
    {
        $spt = $this->sptModel->find($id_spt);
        if (!$spt) {
            return [];
        }

        $nomorSpt = $spt['nomor_spt'] ?? '-';
        $title = 'Pekerjaan Alih Media Dimulai';
        $message = "Proses alih media untuk SPT {$nomorSpt} telah dimulai.";
        $url = base_url('/spt/detail/' . $id_spt);

        $recipients = [];

        // 1. Ambil yang menugaskan dari spt_assignments
        $assignment = $this->db->table('spt_assignments')
            ->where('id_spt', $id_spt)
            ->where('id_user', $arsiparis_user_id)
            ->get()
            ->getRowArray();

        if ($assignment && !empty($assignment['assigned_by'])) {
            $recipients[] = $assignment['assigned_by'];
        }

        // 2. Fallback: Kepala Bidang di OPD/Bidang terkait
        if (empty($recipients)) {
            $arsiparis = $this->userModel->find($arsiparis_user_id);
            if ($arsiparis) {
                $kabidQuery = $this->db->table('users u')
                    ->join('user_roles ur', 'u.id_user = ur.id_user')
                    ->join('roles r', 'ur.id_role = r.id_role')
                    ->whereIn('r.nama_role', ['Kepala_Bidang', 'Kepala Bidang', 'Kabid'])
                    ->where('u.is_active', 1);

                if (!empty($spt['id_opd'])) {
                    $kabidQuery->where('u.id_opd', $spt['id_opd']);
                }
                if (!empty($arsiparis['id_bidang'])) {
                    $kabidQuery->where('u.id_bidang', $arsiparis['id_bidang']);
                }

                $kabids = $kabidQuery->select('u.id_user')->get()->getResultArray();
                foreach ($kabids as $k) {
                    $recipients[] = $k['id_user'];
                }
            }
        }

        $sentIds = [];
        $uniqueRecipients = array_unique(array_filter($recipients));
        foreach ($uniqueRecipients as $kabidId) {
            $sent = $this->sendToUser(
                $kabidId,
                $title,
                $message,
                'spt',
                $id_spt,
                'spt',
                $url,
                $arsiparis_user_id
            );
            if ($sent) {
                $sentIds[] = $sent;
            }
        }

        return $sentIds;
    }

    /**
     * Skenario D: Arsiparis mengirim hasil alih media untuk verifikasi
     * Penerima: Kepala Bidang terkait.
     * Pesan: "Hasil alih media telah dikirim dan menunggu verifikasi."
     * Aksi: Periksa Hasil.
     */
    public function notifyArsipUploaded(string $id_arsip, string $uploader_id): array
    {
        $arsip = $this->arsipModel->find($id_arsip);
        if (!$arsip) {
            return [];
        }

        $nomorArsip = $arsip['nomor_arsip'] ?? 'Tanpa Nomor';
        $namaArsip  = $arsip['nama_arsip'] ?? 'Arsip Baru';

        $title = 'Hasil Alih Media Menunggu Verifikasi';
        $message = "Hasil alih media telah dikirim dan menunggu verifikasi. Berkas: {$nomorArsip} - {$namaArsip}.";
        $url = base_url('/arsip/detail/' . $id_arsip);

        // Cari Kepala Bidang penerima:
        $recipients = [];
        if (!empty($arsip['id_spt'])) {
            $assignment = $this->db->table('spt_assignments')
                ->where('id_spt', $arsip['id_spt'])
                ->get()
                ->getRowArray();
            if ($assignment && !empty($assignment['assigned_by'])) {
                $recipients[] = $assignment['assigned_by'];
            }
        }

        if (empty($recipients)) {
            $uploader = $this->userModel->find($uploader_id);
            if ($uploader) {
                $kabidQuery = $this->db->table('users u')
                    ->join('user_roles ur', 'u.id_user = ur.id_user')
                    ->join('roles r', 'ur.id_role = r.id_role')
                    ->whereIn('r.nama_role', ['Kepala_Bidang', 'Kepala Bidang', 'Kabid'])
                    ->where('u.is_active', 1);

                if (!empty($uploader['id_opd'])) {
                    $kabidQuery->where('u.id_opd', $uploader['id_opd']);
                }
                if (!empty($uploader['id_bidang'])) {
                    $kabidQuery->where('u.id_bidang', $uploader['id_bidang']);
                }

                $kabids = $kabidQuery->select('u.id_user')->get()->getResultArray();
                foreach ($kabids as $k) {
                    $recipients[] = $k['id_user'];
                }
            }
        }

        $sentIds = [];
        $uniqueRecipients = array_unique(array_filter($recipients));
        foreach ($uniqueRecipients as $targetId) {
            $sent = $this->sendToUser(
                $targetId,
                $title,
                $message,
                'arsip',
                $id_arsip,
                'arsip',
                $url,
                $uploader_id
            );
            if ($sent) {
                $sentIds[] = $sent;
            }
        }

        return $sentIds;
    }

    /**
     * Draf Berita Acara Baru dibuat oleh Arsiparis → Notifikasi ke Kepala Bidang
     */
    public function notifyBaDraftCreated(string $id_ba, string $creator_user_id): array
    {
        $ba = $this->baModel->find($id_ba);
        if (!$ba) {
            return [];
        }

        $nomorBa = $ba['nomor_ba'] ?? '-';
        $title = 'Draf Berita Acara Menunggu Verifikasi';
        $message = "Hasil alih media dan draf Berita Acara nomor {$nomorBa} telah dikirim dan menunggu verifikasi.";
        $url = base_url('/berita-acara/detail/' . $id_ba);

        $kabids = $this->db->table('users u')
            ->join('user_roles ur', 'u.id_user = ur.id_user')
            ->join('roles r', 'ur.id_role = r.id_role')
            ->whereIn('r.nama_role', ['Kepala_Bidang', 'Kepala Bidang', 'Kabid'])
            ->where('u.is_active', 1)
            ->where('u.id_opd', $ba['id_opd'])
            ->select('u.id_user')
            ->get()
            ->getResultArray();

        $sentIds = [];
        $uniqueRecipients = array_unique(array_column($kabids, 'id_user'));
        foreach ($uniqueRecipients as $kId) {
            $sent = $this->sendToUser(
                $kId,
                $title,
                $message,
                'berita_acara',
                $id_ba,
                'berita_acara',
                $url,
                $creator_user_id
            );
            if ($sent) {
                $sentIds[] = $sent;
            }
        }

        return $sentIds;
    }

    /**
     * Skenario E & F: Verifikasi Berita Acara oleh Kepala Bidang
     * - E. Kepala Bidang menyetujui hasil/Berita Acara → Pimpinan
     * - F. Kepala Bidang meminta revisi → Arsiparis pelaksana
     */
    public function notifyBaKabidVerified(string $id_ba, string $keputusan, ?string $catatanRevisi = null, ?string $kabid_user_id = null): array
    {
        $ba = $this->baModel->find($id_ba);
        if (!$ba) {
            return [];
        }

        $nomorBa = $ba['nomor_ba'] ?? '-';
        $url = base_url('/berita-acara/detail/' . $id_ba);
        $sentIds = [];

        if ($keputusan === 'Approve') {
            // Skenario E: Pimpinan yang berwenang
            $title = 'Berita Acara Menunggu Pengesahan Pimpinan';
            $message = "Berita Acara telah diverifikasi dan siap ditindaklanjuti oleh Pimpinan. Nomor BA: {$nomorBa}.";

            // Cari Pimpinan pada OPD terkait
            $pimpinan = $this->db->table('users u')
                ->join('user_roles ur', 'u.id_user = ur.id_user')
                ->join('roles r', 'ur.id_role = r.id_role')
                ->whereIn('r.nama_role', ['Pimpinan', 'Kepala_Dinas', 'Kepala Dinas'])
                ->where('u.is_active', 1)
                ->where('u.id_opd', $ba['id_opd'])
                ->select('u.id_user')
                ->get()
                ->getResultArray();

            $uniquePimpinan = array_unique(array_column($pimpinan, 'id_user'));
            foreach ($uniquePimpinan as $pId) {
                $sent = $this->sendToUser(
                    $pId,
                    $title,
                    $message,
                    'berita_acara',
                    $id_ba,
                    'berita_acara',
                    $url,
                    $kabid_user_id
                );
                if ($sent) {
                    $sentIds[] = $sent;
                }
            }
        } else {
            // Skenario F: Arsiparis yang bertanggung jawab atas pekerjaan tersebut
            if (!empty($ba['id_pelaksana'])) {
                $title = 'Permintaan Perbaikan Alih Media';
                $revisiNote = !empty($catatanRevisi) ? " Catatan: {$catatanRevisi}" : '';
                $message = "Hasil alih media memerlukan perbaikan. Silakan periksa catatan revisi.{$revisiNote}";

                $sent = $this->sendToUser(
                    $ba['id_pelaksana'],
                    $title,
                    $message,
                    'berita_acara',
                    $id_ba,
                    'berita_acara',
                    $url,
                    $kabid_user_id
                );
                if ($sent) {
                    $sentIds[] = $sent;
                }
            }
        }

        return $sentIds;
    }

    /**
     * Skenario G: Arsiparis mengirim ulang hasil revisi
     * Penerima: Kepala Bidang terkait.
     * Pesan: "Hasil perbaikan telah dikirim kembali untuk diperiksa."
     * Aksi: Periksa Hasil Revisi.
     */
    public function notifyBaRevisionResubmitted(string $id_ba, string $arsiparis_user_id): array
    {
        $ba = $this->baModel->find($id_ba);
        if (!$ba) {
            return [];
        }

        $nomorBa = $ba['nomor_ba'] ?? '-';
        $title = 'Hasil Perbaikan Dikirim Ulang';
        $message = "Hasil perbaikan telah dikirim kembali untuk diperiksa. Nomor BA: {$nomorBa}.";
        $url = base_url('/berita-acara/detail/' . $id_ba);

        $recipients = [];
        if (!empty($ba['id_kabid'])) {
            $recipients[] = $ba['id_kabid'];
        }

        if (empty($recipients)) {
            $kabids = $this->db->table('users u')
                ->join('user_roles ur', 'u.id_user = ur.id_user')
                ->join('roles r', 'ur.id_role = r.id_role')
                ->whereIn('r.nama_role', ['Kepala_Bidang', 'Kepala Bidang', 'Kabid'])
                ->where('u.is_active', 1)
                ->where('u.id_opd', $ba['id_opd'])
                ->select('u.id_user')
                ->get()
                ->getResultArray();

            foreach ($kabids as $k) {
                $recipients[] = $k['id_user'];
            }
        }

        $sentIds = [];
        $uniqueRecipients = array_unique(array_filter($recipients));
        foreach ($uniqueRecipients as $kabidId) {
            $sent = $this->sendToUser(
                $kabidId,
                $title,
                $message,
                'berita_acara',
                $id_ba,
                'berita_acara',
                $url,
                $arsiparis_user_id
            );
            if ($sent) {
                $sentIds[] = $sent;
            }
        }

        return $sentIds;
    }

    /**
     * Skenario H: Pimpinan menandatangani Berita Acara
     * Penerima: Kepala Bidang dan Arsiparis yang berkaitan dengan Berita Acara tersebut, serta Admin OPD.
     * Pesan: "Berita Acara telah ditandatangani oleh Pimpinan."
     * Aksi: Lihat Berita Acara.
     */
    public function notifyBaPimpinanSigned(string $id_ba, string $pimpinan_user_id): array
    {
        $ba = $this->baModel->find($id_ba);
        if (!$ba) {
            return [];
        }

        $nomorBa = $ba['nomor_ba'] ?? '-';
        $title = 'Berita Acara Telah Ditandatangani';
        $message = "Berita Acara telah ditandatangani oleh Pimpinan. Nomor BA: {$nomorBa}.";
        $url = base_url('/berita-acara/detail/' . $id_ba);

        $recipients = [];

        // 1. Kepala Bidang yang memverifikasi
        if (!empty($ba['id_kabid'])) {
            $recipients[] = $ba['id_kabid'];
        }

        // 2. Arsiparis pelaksana pembuat BA
        if (!empty($ba['id_pelaksana'])) {
            $recipients[] = $ba['id_pelaksana'];
        }

        // 3. Admin OPD dari OPD bersangkutan
        if (!empty($ba['id_opd'])) {
            $adminOpds = $this->db->table('users u')
                ->join('user_roles ur', 'u.id_user = ur.id_user')
                ->join('roles r', 'ur.id_role = r.id_role')
                ->whereIn('r.nama_role', ['Admin_OPD', 'Admin OPD', 'Administrator OPD'])
                ->where('u.id_opd', $ba['id_opd'])
                ->where('u.is_active', 1)
                ->select('u.id_user')
                ->get()
                ->getResultArray();

            foreach ($adminOpds as $adm) {
                $recipients[] = $adm['id_user'];
            }
        }

        $sentIds = [];
        $uniqueRecipients = array_unique(array_filter($recipients));
        foreach ($uniqueRecipients as $targetId) {
            $sent = $this->sendToUser(
                $targetId,
                $title,
                $message,
                'berita_acara',
                $id_ba,
                'berita_acara',
                $url,
                $pimpinan_user_id
            );
            if ($sent) {
                $sentIds[] = $sent;
            }
        }

        return $sentIds;
    }

    /**
     * Skenario I: Pimpinan menolak atau meminta perbaikan Berita Acara
     * Penerima: Kepala Bidang yang mengajukan atau memverifikasi Berita Acara.
     * Pesan: "Berita Acara memerlukan tindak lanjut. Silakan periksa catatan dari Pimpinan."
     * Aksi: Lihat Catatan.
     */
    public function notifyBaPimpinanRejected(string $id_ba, string $pimpinan_user_id, ?string $catatan = null): array
    {
        $ba = $this->baModel->find($id_ba);
        if (!$ba) {
            return [];
        }

        $nomorBa = $ba['nomor_ba'] ?? '-';
        $title = 'Catatan Tindak Lanjut Berita Acara';
        $catatanText = !empty($catatan) ? " Catatan: {$catatan}" : "";
        $message = "Berita Acara memerlukan tindak lanjut. Silakan periksa catatan dari Pimpinan.{$catatanText}";
        $url = base_url('/berita-acara/detail/' . $id_ba);

        $recipients = [];
        if (!empty($ba['id_kabid'])) {
            $recipients[] = $ba['id_kabid'];
        }

        if (empty($recipients)) {
            $kabids = $this->db->table('users u')
                ->join('user_roles ur', 'u.id_user = ur.id_user')
                ->join('roles r', 'ur.id_role = r.id_role')
                ->whereIn('r.nama_role', ['Kepala_Bidang', 'Kepala Bidang', 'Kabid'])
                ->where('u.is_active', 1)
                ->where('u.id_opd', $ba['id_opd'])
                ->select('u.id_user')
                ->get()
                ->getResultArray();

            foreach ($kabids as $k) {
                $recipients[] = $k['id_user'];
            }
        }

        $sentIds = [];
        $uniqueRecipients = array_unique(array_filter($recipients));
        foreach ($uniqueRecipients as $kabidId) {
            $sent = $this->sendToUser(
                $kabidId,
                $title,
                $message,
                'berita_acara',
                $id_ba,
                'berita_acara',
                $url,
                $pimpinan_user_id
            );
            if ($sent) {
                $sentIds[] = $sent;
            }
        }

        return $sentIds;
    }

    /**
     * Verifikasi Berkas Arsip oleh Kepala Bidang (Disetujui) → Notifikasi ke Arsiparis
     */
    public function notifyArsipVerified(string $id_arsip, string $kabid_user_id): ?string
    {
        $arsip = $this->arsipModel->find($id_arsip);
        if (!$arsip) {
            return null;
        }

        $targetUser = $arsip['id_user_upload'] ?? $arsip['created_by'];
        if (empty($targetUser)) {
            return null;
        }

        $nomorArsip = $arsip['nomor_arsip'] ?? '-';
        $namaArsip  = $arsip['nama_arsip'] ?? '-';

        $title = 'Verifikasi Alih Media Disetujui';
        $message = "Hasil alih media berkas '{$namaArsip}' ({$nomorArsip}) telah diverifikasi dan disetujui oleh Kepala Bidang.";
        $url = base_url('/arsip/detail/' . $id_arsip);

        return $this->sendToUser(
            $targetUser,
            $title,
            $message,
            'arsip',
            $id_arsip,
            'arsip',
            $url,
            $kabid_user_id
        );
    }

    /**
     * Notifikasi Pengelolaan Akun Pengguna (Admin Pemkab & Admin OPD)
     */
    public function notifyUserManagement(string $target_user_id, string $actionType = 'create', ?string $actor_id = null): array
    {
        $targetUser = $this->userModel->find($target_user_id);
        if (!$targetUser) {
            return [];
        }

        $namaUser = $targetUser['nama'] ?? 'Pengguna';
        $actionText = ($actionType === 'create') ? 'dibuat' : (($actionType === 'reset') ? 'direset password-nya' : 'diperbarui');

        $title = 'Aktivitas Pengelolaan Akun';
        $message = "Akun pengguna {$namaUser} telah {$actionText} oleh Administrator.";
        $url = base_url('/users');

        $recipients = [];

        // Jika user memiliki OPD, kirim notifikasi ke Admin OPD bersangkutan (kecuali aktor yang melakukan aksi itu sendiri)
        if (!empty($targetUser['id_opd'])) {
            $adminOpds = $this->db->table('users u')
                ->join('user_roles ur', 'u.id_user = ur.id_user')
                ->join('roles r', 'ur.id_role = r.id_role')
                ->whereIn('r.nama_role', ['Admin_OPD', 'Admin OPD', 'Administrator OPD'])
                ->where('u.id_opd', $targetUser['id_opd'])
                ->where('u.is_active', 1)
                ->select('u.id_user')
                ->get()
                ->getResultArray();

            foreach ($adminOpds as $adm) {
                if ($adm['id_user'] !== $actor_id) {
                    $recipients[] = $adm['id_user'];
                }
            }
        }

        $sentIds = [];
        $uniqueRecipients = array_unique(array_filter($recipients));
        foreach ($uniqueRecipients as $targetId) {
            $sent = $this->sendToUser(
                $targetId,
                $title,
                $message,
                'user',
                $target_user_id,
                'user',
                $url,
                $actor_id
            );
            if ($sent) {
                $sentIds[] = $sent;
            }
        }

        return $sentIds;
    }
}
