<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SptModel;
use App\Models\SptPelaksanaModel; // TODO: Can be removed later
use App\Models\UserModel;
use App\Models\SptAssignmentModel;

class Spt extends BaseController
{
    protected $sptModel;
    protected $sptPelaksanaModel;
    protected $userModel;
    protected $sptAssignmentModel;

    public function __construct()
    {
        $this->sptModel = new SptModel();
        $this->sptPelaksanaModel = new SptPelaksanaModel();
        $this->userModel = new UserModel();
        $this->sptAssignmentModel = new SptAssignmentModel();
        helper('logger');
    }

    public function index()
    {
        $session = session();
        $nama_role = $session->get('nama_role');
        $id_opd  = $session->get('id_opd');
        $user_id = $session->get('user_id');

        // Jika Pimpinan atau Admin OPD atau Kabid -> Tampilkan semua SPT di OPD tersebut
        if (in_array($nama_role, ['Pimpinan', 'Admin_OPD', 'Kepala_Bidang'])) {
            $data['spt_list'] = $this->sptModel->getSptByOpd($id_opd);
        } 
        // Jika Arsiparis -> Tampilkan hanya SPT miliknya
        elseif ($nama_role === 'Arsiparis') {
            $data['spt_list'] = $this->sptModel->getSptByPelaksana($user_id);
        }
        // Jika Admin Pemkab -> Bisa lihat semua (opsional, asumsikan bisa lihat semua)
        else {
            $data['spt_list'] = $this->sptModel->orderBy('tanggal_spt', 'DESC')->findAll();
        }

        // Hitung Statistik Informasi SPT
        $totalSpt = count($data['spt_list'] ?? []);
        $belumDitugaskan = 0;
        $ditugaskan = 0;
        $selesai = 0;

        foreach ($data['spt_list'] as $item) {
            $status = strtolower(trim($item['status_penugasan'] ?? ''));
            if ($status === 'selesai') {
                $selesai++;
            } elseif (in_array($status, ['ditugaskan', 'proses', 'dalam proses'])) {
                $ditugaskan++;
            } else {
                $belumDitugaskan++;
            }
        }

        $data['totalSpt'] = $totalSpt;
        $data['belumDitugaskan'] = $belumDitugaskan;
        $data['ditugaskan'] = $ditugaskan;
        $data['selesai'] = $selesai;
        $data['user_role'] = $nama_role;

        return view('spt/index', $data);
    }

    public function create()
    {
        $session = session();
        
        // Proteksi: Hanya Pimpinan / Kepala Dinas yang boleh
        if (!in_array($session->get('nama_role'), ['Pimpinan', 'Kepala_Dinas', 'Kepala Dinas'])) {
            return redirect()->to('/spt')->with('error', 'Akses ditolak. Hanya Pimpinan yang dapat menerbitkan SPT.');
        }

        $id_opd  = $session->get('id_opd');

        // Ambil daftar pengguna dengan role Arsiparis (id_role = 5) di OPD yang sama
        // Query builder untuk mendapatkan user arsiparis
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('users.*');
        $builder->join('user_roles', 'user_roles.id_user = users.id_user');
        $builder->join('roles', 'roles.id_role = user_roles.id_role');
        $builder->where('roles.nama_role', 'Arsiparis');
        
        if ($id_opd !== null) {
            $builder->where('users.id_opd', $id_opd);
        }
        
        $data['arsiparis_list'] = $builder->get()->getResultArray();

        return view('spt/create', $data);
    }

    public function store()
    {
        $session = session();

        // Proteksi: Hanya Pimpinan / Kepala Dinas yang boleh
        if (!in_array($session->get('nama_role'), ['Pimpinan', 'Kepala_Dinas', 'Kepala Dinas'])) {
            return redirect()->to('/spt')->with('error', 'Akses ditolak. Hanya Pimpinan yang dapat menerbitkan SPT.');
        }

        // Validasi dan Proses File SPT
        $fileSpt = $this->request->getFile('file_spt');
        if (!$fileSpt || !$fileSpt->isValid()) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengunggah file. ' . ($fileSpt ? $fileSpt->getErrorString() : 'File SPT wajib diunggah.'));
        }

        // Cek ekstensi dan ukuran (format PDF, maks 5MB)
        $ext = strtolower($fileSpt->getClientExtension() ?: $fileSpt->getExtension());
        if ($ext !== 'pdf' && $fileSpt->getClientMimeType() !== 'application/pdf') {
            return redirect()->back()->withInput()->with('error', 'Format file harus PDF.');
        }

        if ($fileSpt->getSize() > 5 * 1024 * 1024) {
            return redirect()->back()->withInput()->with('error', 'Ukuran file melebihi batas maksimal 5MB.');
        }

        // Pastikan direktori tujuan tersedia
        $uploadDir = FCPATH . 'uploads/spt';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate nama unik dan pindahkan file
        $fileName = $fileSpt->getRandomName();
        $fileSpt->move($uploadDir, $fileName);
        
        // Data Utama SPT
        $sptData = [
            'nomor_spt'        => trim((string)$this->request->getPost('nomor_spt')),
            'tanggal_spt'      => $this->request->getPost('tanggal_spt'),
            'perihal'          => trim((string)$this->request->getPost('perihal')),
            'tanggal_mulai'    => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai'  => $this->request->getPost('tanggal_selesai'),
            'status'           => 'Aktif',
            'status_penugasan' => 'belum_ditugaskan',
            'id_opd'           => $session->get('id_opd'),
            'id_pimpinan'      => $session->get('user_id'), // Yang menerbitkan
            'file_spt'         => $fileName
        ];

        // Validasi dan Insert SPT
        if (!$this->sptModel->insert($sptData)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data SPT. Periksa kembali inputan Anda.');
        }

        $id_spt = $this->sptModel->getInsertID();

        log_activity('SPT', 'CREATE', 'Menerbitkan Surat Perintah Tugas baru: ' . $sptData['nomor_spt']);

        return redirect()->to('/spt')->with('success', 'SPT berhasil diterbitkan. Menunggu penugasan oleh Kepala Bidang.');
    }

    public function detail($id)
    {
        $session = session();
        $data['spt'] = $this->sptModel->find($id);

        if (!$data['spt']) {
            return redirect()->to('/spt')->with('error', 'SPT tidak ditemukan.');
        }

        $db = \Config\Database::connect();

        // Ambil nama pimpinan penerbit jika ada
        $pimpinan = null;
        if (!empty($data['spt']['id_pimpinan'])) {
            $pimpinan = $db->table('users')->where('id_user', $data['spt']['id_pimpinan'])->get()->getRowArray();
        }
        $data['pimpinan'] = $pimpinan;

        // Ambil data OPD jika ada
        $opd = null;
        if (!empty($data['spt']['id_opd'])) {
            $opd = $db->table('opd')->where('id_opd', $data['spt']['id_opd'])->get()->getRowArray();
        }
        $data['opd'] = $opd;

        // Ambil daftar pelaksana dari tabel spt_assignments
        $builder = $db->table('spt_assignments');
        $builder->select('users.nama, users.email, bidang.nama_bidang, spt_assignments.status, spt_assignments.created_at as tgl_ditugaskan, assigner.nama as nama_assigner');
        $builder->join('users', 'users.id_user = spt_assignments.id_user');
        $builder->join('bidang', 'bidang.id_bidang = users.id_bidang', 'left');
        $builder->join('users as assigner', 'assigner.id_user = spt_assignments.assigned_by', 'left');
        $builder->where('spt_assignments.id_spt', $id);
        
        $data['pelaksana_list'] = $builder->get()->getResultArray();
        $data['user_role']      = $session->get('nama_role');

        return view('spt/detail', $data);
    }

    public function delete($id)
    {
        $session = session();
        
        // Proteksi: Hanya Pimpinan yang boleh
        if ($session->get('nama_role') !== 'Pimpinan') {
            return redirect()->to('/spt')->with('error', 'Akses ditolak. Hanya Pimpinan yang dapat menghapus SPT.');
        }

        $spt = $this->sptModel->find($id);

        if (!$spt) {
            return redirect()->to('/spt')->with('error', 'SPT tidak ditemukan.');
        }

        // Pastikan pimpinan hanya bisa hapus SPT di OPD-nya
        if ($spt['id_opd'] != $session->get('id_opd')) {
            return redirect()->to('/spt')->with('error', 'Anda tidak berhak menghapus SPT dari OPD lain.');
        }

        // 1. Hapus file fisik PDF jika ada
        if (!empty($spt['file_spt'])) {
            $filePath = FCPATH . 'uploads/spt/' . $spt['file_spt'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // 2. Hapus data pelaksana di tabel pivot
        $this->sptPelaksanaModel->where('id_spt', $id)->delete();

        // 3. Hapus data utama SPT
        $this->sptModel->delete($id);

        log_activity('SPT', 'DELETE', 'Menghapus Surat Perintah Tugas: ' . $spt['nomor_spt']);

        return redirect()->to('/spt')->with('success', 'Data SPT dan file PDF lampirannya berhasil dihapus.');
    }

    public function assign($id_spt)
    {
        $session = session();
        $nama_role = $session->get('nama_role');
        $id_opd  = $session->get('id_opd');
        $id_bidang = $session->get('id_bidang');

        if (!in_array($nama_role, ['Kepala_Bidang', 'Kepala Bidang', 'Kabid'])) {
            return redirect()->to('/spt')->with('error', 'Akses ditolak. Hanya Kepala Bidang yang dapat menugaskan Arsiparis.');
        }

        $spt = $this->sptModel->find($id_spt);
        if (!$spt || $spt['status_penugasan'] !== 'belum_ditugaskan') {
            return redirect()->to('/spt')->with('error', 'SPT tidak valid atau sudah ditugaskan.');
        }

        $db = \Config\Database::connect();

        // Ambil nama bidang jika login sebagai Kepala Bidang
        $nama_bidang = null;
        if ($id_bidang) {
            $bidangRow = $db->table('bidang')->where('id_bidang', $id_bidang)->get()->getRowArray();
            $nama_bidang = $bidangRow['nama_bidang'] ?? null;
        }

        // Ambil daftar Arsiparis
        $builder = $db->table('users');
        $builder->select('users.*, bidang.nama_bidang');
        $builder->join('user_roles', 'user_roles.id_user = users.id_user');
        $builder->join('roles', 'roles.id_role = user_roles.id_role');
        $builder->join('bidang', 'bidang.id_bidang = users.id_bidang', 'left');
        $builder->where('roles.nama_role', 'Arsiparis');
        $builder->where('users.is_active', 1);

        if ($id_opd !== null) {
            $builder->where('users.id_opd', $id_opd);
        }

        // Jika Kepala Bidang: Filter HANYA Arsiparis di bidangnya
        if ($nama_role === 'Kepala_Bidang' && $id_bidang) {
            $builder->where('users.id_bidang', $id_bidang);
        }
        
        $data['arsiparis_list'] = $builder->get()->getResultArray();
        $data['spt']            = $spt;
        $data['id_bidang']      = $id_bidang;
        $data['nama_bidang']    = $nama_bidang;
        $data['user_role']      = $nama_role;

        return view('spt/assign', $data);
    }

    public function processAssign($id_spt)
    {
        $session = session();
        $nama_role = $session->get('nama_role');
        if (!in_array($nama_role, ['Kepala_Bidang', 'Kepala Bidang', 'Kabid'])) {
            return redirect()->to('/spt')->with('error', 'Akses ditolak. Hanya Kepala Bidang yang berhak menugaskan Arsiparis.');
        }

        $id_user = $this->request->getPost('id_user');
        if (empty($id_user)) {
            return redirect()->back()->with('error', 'Silakan pilih Arsiparis.');
        }

        // Validasi: jika Kepala_Bidang, pastikan arsiparis yang dipilih benar-benar di bidangnya
        if ($nama_role === 'Kepala_Bidang') {
            $id_bidang = $session->get('id_bidang');
            $targetUser = $this->userModel->find($id_user);
            if (!$targetUser || ($id_bidang && $targetUser['id_bidang'] !== $id_bidang)) {
                return redirect()->back()->with('error', 'Arsiparis yang dipilih bukan berada di bidang Anda.');
            }
        }

        // Simpan ke spt_assignments
        $assignmentData = [
            'id_spt' => $id_spt,
            'id_user' => $id_user,
            'assigned_by' => $session->get('user_id'),
            'status' => 'pending'
        ];

        if (!$this->sptAssignmentModel->insert($assignmentData)) {
            return redirect()->back()->with('error', 'Gagal menugaskan Arsiparis.');
        }

        // Update status SPT
        $this->sptModel->update($id_spt, ['status_penugasan' => 'ditugaskan']);

        $spt = $this->sptModel->find($id_spt);
        $arsiparis = $this->userModel->find($id_user);
        $namaArsiparis = $arsiparis['nama'] ?? 'Arsiparis';
        $nomorSpt = $spt['nomor_spt'] ?? $id_spt;
        log_activity('SPT', 'ASSIGN', "Menugaskan pelaksana {$namaArsiparis} pada SPT: " . $nomorSpt);

        // Kirim Notifikasi ke Arsiparis
        try {
            $notifModel = new \App\Models\NotificationModel();
            $notifModel->insert([
                'id_user'     => $id_user,
                'title'       => 'Penugasan SPT Baru',
                'message'     => "Anda telah ditugaskan pada Surat Perintah Tugas: {$nomorSpt}.",
                'is_read'     => 0,
                'action_type' => 'spt',
                'action_id'   => $id_spt
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mengirim notifikasi SPT: ' . $e->getMessage());
        }

        return redirect()->to('/spt')->with('success', "SPT {$nomorSpt} berhasil ditugaskan kepada {$namaArsiparis}.");
    }

    public function startProcess($id_spt)
    {
        $session = session();
        if ($session->get('nama_role') !== 'Arsiparis') {
            return redirect()->to('/spt')->with('error', 'Akses ditolak.');
        }

        // Cari assignment terkait
        $assignment = $this->sptAssignmentModel->where('id_spt', $id_spt)
                                               ->where('id_user', $session->get('user_id'))
                                               ->first();

        if (!$assignment) {
            return redirect()->to('/spt')->with('error', 'Tugas tidak ditemukan.');
        }

        // Ubah status assignment ke proses
        $this->sptAssignmentModel->update($assignment['id_assignment'], ['status' => 'proses']);

        // Redirect ke halaman buat arsip dengan param spt_id
        return redirect()->to('/arsip/create?id_spt=' . $id_spt);
    }
}
