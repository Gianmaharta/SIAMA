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
     */
    public function sendToUser(
        string $userId,
        string $title,
        string $message,
        string $type = 'general',
        ?string $referenceId = null,
        ?string $referenceType = null,
        ?string $url = null
    ): ?string {
        if (empty($userId) || empty($title) || empty($message)) {
            return null;
        }

        // Cek apakah notifikasi identik persis sudah pernah dikirim dalam 5 menit terakhir (cegah duplikasi)
        $existing = $this->db->table('notifications')
            ->where('id_user', $userId)
            ->where('title', $title)
            ->where('reference_id', $referenceId)
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-5 minutes')))
            ->get()
            ->getRowArray();

        if ($existing) {
            return $existing['id_notification'];
        }

        return $this->notifModel->send([
            'id_user'        => $userId,
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

    /**
     * Skenario A: Kepala Bidang → Arsiparis (Penugasan Alih Media Baru)
     */
    public function notifySptAssigned(string $id_spt, string $id_user_arsiparis, ?string $assigned_by_id = null): ?string
    {
        $spt = $this->sptModel->find($id_spt);
        if (!$spt) {
            return null;
        }

        $namaOpd = '-';
        if (!empty($spt['id_opd'])) {
            $opd = $this->opdModel->find($spt['id_opd']);
            if ($opd) {
                $namaOpd = $opd['nama_opd'];
            }
        }

        $nomorSpt = $spt['nomor_spt'] ?? '-';
        $tanggalSpt = !empty($spt['tanggal_spt']) ? date('d/m/Y', strtotime($spt['tanggal_spt'])) : date('d/m/Y');

        $title = 'Penugasan Alih Media Baru';
        $message = "Anda mendapatkan penugasan alih media arsip baru. Nomor SPT: {$nomorSpt}, OPD: {$namaOpd}, Tanggal: {$tanggalSpt}.";
        $url = base_url('/spt/detail/' . $id_spt);

        return $this->sendToUser(
            $id_user_arsiparis,
            $title,
            $message,
            'spt',
            $id_spt,
            'spt',
            $url
        );
    }

    /**
     * Skenario B: Arsiparis → Kepala Bidang (Hasil Alih Media Terkirim & Menunggu Verifikasi)
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
        $message = "Hasil alih media arsip telah dikirim dan menunggu verifikasi. Berkas: {$nomorArsip} - {$namaArsip}.";
        $url = base_url('/arsip/detail/' . $id_arsip);

        // Cari Kepala Bidang penerima:
        // 1. Jika terikat SPT, periksa siapa yang menugaskan di spt_assignments
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

        // 2. Jika belum ada penerima, cari Kepala Bidang di OPD & Bidang pengunggah
        if (empty($recipients)) {
            $uploader = $this->userModel->find($uploader_id);
            if ($uploader) {
                $kabidQuery = $this->db->table('users u')
                    ->join('user_roles ur', 'u.id_user = ur.id_user')
                    ->join('roles r', 'ur.id_role = r.id_role')
                    ->where('r.nama_role', 'Kepala_Bidang')
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
                $url
            );
            if ($sent) {
                $sentIds[] = $sent;
            }
        }

        return $sentIds;
    }

    /**
     * Skenario C1: Kepala Bidang → Arsiparis (Verifikasi Berkas Arsip Disetujui)
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
            $url
        );
    }

    /**
     * Skenario C2 & D: Verifikasi Berita Acara oleh Kepala Bidang
     * - Jika Approve: Notifikasi ke Pimpinan (Skenario D)
     * - Jika Revisi: Notifikasi ke Arsiparis / Pelaksana (Skenario C2)
     */
    public function notifyBaKabidVerified(string $id_ba, string $keputusan, ?string $catatanRevisi = null): array
    {
        $ba = $this->baModel->find($id_ba);
        if (!$ba) {
            return [];
        }

        $nomorBa = $ba['nomor_ba'] ?? '-';
        $url = base_url('/berita-acara/detail/' . $id_ba);
        $sentIds = [];

        if ($keputusan === 'Approve') {
            // Skenario D: Kirim notifikasi ke Pimpinan / Kepala Dinas
            $title = 'Berita Acara Menunggu Pengesahan';
            $message = "Berita Acara baru nomor {$nomorBa} telah diverifikasi oleh Kepala Bidang dan menunggu pengesahan Anda.";

            // Cari Pimpinan pada OPD terkait
            $pimpinan = $this->db->table('users u')
                ->join('user_roles ur', 'u.id_user = ur.id_user')
                ->join('roles r', 'ur.id_role = r.id_role')
                ->where('r.nama_role', 'Pimpinan')
                ->where('u.is_active', 1)
                ->where('u.id_opd', $ba['id_opd'])
                ->select('u.id_user')
                ->get()
                ->getResultArray();

            foreach ($pimpinan as $p) {
                $sent = $this->sendToUser(
                    $p['id_user'],
                    $title,
                    $message,
                    'berita_acara',
                    $id_ba,
                    'berita_acara',
                    $url
                );
                if ($sent) {
                    $sentIds[] = $sent;
                }
            }
        } else {
            // Skenario C2: Kembalikan untuk revisi ke Arsiparis / Pelaksana pembuat BA
            if (!empty($ba['id_pelaksana'])) {
                $title = 'Revisi Berita Acara Alih Media';
                $revisiNote = !empty($catatanRevisi) ? " Catatan: {$catatanRevisi}" : '';
                $message = "Berita Acara nomor {$nomorBa} dikembalikan oleh Kepala Bidang untuk revisi.{$revisiNote}";

                $sent = $this->sendToUser(
                    $ba['id_pelaksana'],
                    $title,
                    $message,
                    'berita_acara',
                    $id_ba,
                    'berita_acara',
                    $url
                );
                if ($sent) {
                    $sentIds[] = $sent;
                }
            }
        }

        return $sentIds;
    }

    /**
     * Skenario E: Pimpinan → Kepala Bidang & Arsiparis (Pengesahan Berita Acara Selesai)
     */
    public function notifyBaPimpinanSigned(string $id_ba, string $pimpinan_user_id): array
    {
        $ba = $this->baModel->find($id_ba);
        if (!$ba) {
            return [];
        }

        $nomorBa = $ba['nomor_ba'] ?? '-';
        $title = 'Berita Acara Berhasil Disahkan';
        $message = "Berita Acara alih media nomor {$nomorBa} telah resmi disahkan oleh Pimpinan/Kepala Dinas.";
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
                ->where('r.nama_role', 'Admin_OPD')
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
                $url
            );
            if ($sent) {
                $sentIds[] = $sent;
            }
        }

        return $sentIds;
    }

    /**
     * Draf Berita Acara Baru dibuat oleh Arsiparis → Notifikasi ke Kepala Bidang di OPD
     */
    public function notifyBaDraftCreated(string $id_ba, string $creator_user_id): array
    {
        $ba = $this->baModel->find($id_ba);
        if (!$ba) {
            return [];
        }

        $nomorBa = $ba['nomor_ba'] ?? '-';
        $title = 'Draf Berita Acara Menunggu Verifikasi';
        $message = "Draf Berita Acara nomor {$nomorBa} telah dibuat dan menunggu verifikasi Kepala Bidang.";
        $url = base_url('/berita-acara/detail/' . $id_ba);

        $kabids = $this->db->table('users u')
            ->join('user_roles ur', 'u.id_user = ur.id_user')
            ->join('roles r', 'ur.id_role = r.id_role')
            ->where('r.nama_role', 'Kepala_Bidang')
            ->where('u.is_active', 1)
            ->where('u.id_opd', $ba['id_opd'])
            ->select('u.id_user')
            ->get()
            ->getResultArray();

        $sentIds = [];
        foreach ($kabids as $k) {
            $sent = $this->sendToUser(
                $k['id_user'],
                $title,
                $message,
                'berita_acara',
                $id_ba,
                'berita_acara',
                $url
            );
            if ($sent) {
                $sentIds[] = $sent;
            }
        }

        return $sentIds;
    }

    /**
     * Skenario F: Notifikasi Pengelolaan Akun Pengguna (Admin Pemkab & Admin OPD)
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
                ->where('r.nama_role', 'Admin_OPD')
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
                $url
            );
            if ($sent) {
                $sentIds[] = $sent;
            }
        }

        return $sentIds;
    }
}

