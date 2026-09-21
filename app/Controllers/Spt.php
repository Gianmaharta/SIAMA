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
            $data['spt_list'] = $this->sptModel->findAll();
        }

        return view('spt/index', $data);
    }

    public function create()
    {
        $session = session();
        
        // Proteksi: Hanya Pimpinan yang boleh
        if ($session->get('nama_role') !== 'Pimpinan') {
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

        // Proteksi: Hanya Pimpinan yang boleh
        if ($session->get('nama_role') !== 'Pimpinan') {
            return redirect()->to('/spt')->with('error', 'Akses ditolak. Hanya Pimpinan yang dapat menerbitkan SPT.');
        }

        // Validasi dan Proses File SPT
        $fileSpt = $this->request->getFile('file_spt');
        if (!$fileSpt->isValid()) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengunggah file. ' . $fileSpt->getErrorString());
        }

        // Cek ekstensi dan ukuran
        if ($fileSpt->getExtension() !== 'pdf') {
            return redirect()->back()->withInput()->with('error', 'Format file harus PDF.');
        }

        // Generate nama unik dan pindahkan file
        $fileName = $fileSpt->getRandomName();
        $fileSpt->move(FCPATH . 'uploads/spt', $fileName);
        
        // Data Utama SPT
        $sptData = [
            'nomor_spt'       => $this->request->getPost('nomor_spt'),
            'tanggal_spt'     => $this->request->getPost('tanggal_spt'),
            'perihal'         => $this->request->getPost('perihal'),
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'status'          => 'Aktif',
            'id_opd'          => $session->get('id_opd'),
            'id_pimpinan'     => $session->get('user_id'), // Yang menerbitkan
            'file_spt'        => $fileName
        ];

        // Validasi dan Insert SPT
        if (!$this->sptModel->insert($sptData)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data SPT. Periksa kembali inputan Anda.');
        }

        $id_spt = $this->sptModel->getInsertID();

        return redirect()->to('/spt')->with('success', 'SPT berhasil diterbitkan. Menunggu penugasan oleh Kepala Bidang.');
    }

    public function detail($id)
    {
        $data['spt'] = $this->sptModel->find($id);

        if (!$data['spt']) {
            return redirect()->to('/spt')->with('error', 'SPT tidak ditemukan.');
        }

        // Ambil daftar pelaksana dari tabel spt_assignments
        $db = \Config\Database::connect();
        $builder = $db->table('spt_assignments');
        $builder->select('users.nama, users.email, spt_assignments.status');
        $builder->join('users', 'users.id_user = spt_assignments.id_user');
        $builder->where('spt_assignments.id_spt', $id);
        
        $data['pelaksana_list'] = $builder->get()->getResultArray();

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

        return redirect()->to('/spt')->with('success', 'Data SPT dan file PDF lampirannya berhasil dihapus.');
    }

    public function assign($id_spt)
    {
        $session = session();
        $nama_role = $session->get('nama_role');
        $id_opd  = $session->get('id_opd');

        if (!in_array($nama_role, ['Kepala_Bidang', 'Admin_OPD'])) {
            return redirect()->to('/spt')->with('error', 'Akses ditolak. Hanya Kabid/Admin OPD yang dapat menugaskan Arsiparis.');
        }

        $spt = $this->sptModel->find($id_spt);
        if (!$spt || $spt['status_penugasan'] !== 'belum_ditugaskan') {
            return redirect()->to('/spt')->with('error', 'SPT tidak valid atau sudah ditugaskan.');
        }

        // Ambil daftar Arsiparis di OPD yang sama
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
        $data['spt'] = $spt;

        return view('spt/assign', $data);
    }

    public function processAssign($id_spt)
    {
        $session = session();
        if (!in_array($session->get('nama_role'), ['Kepala_Bidang', 'Admin_OPD'])) {
            return redirect()->to('/spt')->with('error', 'Akses ditolak.');
        }

        $id_user = $this->request->getPost('id_user');
        if (empty($id_user)) {
            return redirect()->back()->with('error', 'Silakan pilih Arsiparis.');
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

        return redirect()->to('/spt')->with('success', 'SPT berhasil ditugaskan ke Arsiparis.');
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
