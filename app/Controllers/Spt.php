<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SptModel;
use App\Models\SptPelaksanaModel;
use App\Models\UserModel;

class Spt extends BaseController
{
    protected $sptModel;
    protected $sptPelaksanaModel;
    protected $userModel;

    public function __construct()
    {
        $this->sptModel = new SptModel();
        $this->sptPelaksanaModel = new SptPelaksanaModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $session = session();
        $id_role = $session->get('id_role');
        $id_opd  = $session->get('id_opd');
        $user_id = $session->get('user_id');

        // Jika Pimpinan (2) atau Admin OPD (3) atau Kabid (4) -> Tampilkan semua SPT di OPD tersebut
        if (in_array($id_role, [2, 3, 4])) {
            $data['spt_list'] = $this->sptModel->getSptByOpd($id_opd);
        } 
        // Jika Arsiparis (5) -> Tampilkan hanya SPT miliknya
        elseif ($id_role == 5) {
            $data['spt_list'] = $this->sptModel->getSptByPelaksana($user_id);
        }
        // Jika Admin Pemkab (1) -> Bisa lihat semua (opsional, asumsikan bisa lihat semua)
        else {
            $data['spt_list'] = $this->sptModel->findAll();
        }

        return view('spt/index', $data);
    }

    public function create()
    {
        $session = session();
        $id_opd  = $session->get('id_opd');

        // Ambil daftar pengguna dengan role Arsiparis (id_role = 5) di OPD yang sama
        // Query builder untuk mendapatkan user arsiparis
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('users.*');
        $builder->join('user_roles', 'user_roles.id_user = users.id_user');
        $builder->where('user_roles.id_role', 5);
        
        if ($id_opd !== null) {
            $builder->where('users.id_opd', $id_opd);
        }
        
        $data['arsiparis_list'] = $builder->get()->getResultArray();

        return view('spt/create', $data);
    }

    public function store()
    {
        $session = session();
        
        // Data Utama SPT
        $sptData = [
            'nomor_spt'       => $this->request->getPost('nomor_spt'),
            'tanggal_spt'     => $this->request->getPost('tanggal_spt'),
            'perihal'         => $this->request->getPost('perihal'),
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'status'          => 'Aktif',
            'id_opd'          => $session->get('id_opd'),
            'id_pimpinan'     => $session->get('user_id') // Yang menerbitkan
        ];

        // Validasi dan Insert SPT
        if (!$this->sptModel->insert($sptData)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data SPT. Periksa kembali inputan Anda.');
        }

        $id_spt = $this->sptModel->getInsertID();

        // Ambil array pelaksana dari checkbox
        $pelaksana = $this->request->getPost('pelaksana'); // array of user_id

        if (!empty($pelaksana)) {
            $pelaksanaData = [];
            foreach ($pelaksana as $user_id) {
                // Ambil id_bidang dari user ini untuk diisi ke spt_pelaksana
                $user = $this->userModel->find($user_id);
                
                $pelaksanaData[] = [
                    'id_spt'    => $id_spt,
                    'id_user'   => $user_id,
                    'id_bidang' => $user['id_bidang'] ?? 0 // Default 0 jika null
                ];
            }
            $this->sptPelaksanaModel->insertBatch($pelaksanaData);
        }

        return redirect()->to('/spt')->with('success', 'SPT berhasil diterbitkan.');
    }

    public function detail($id)
    {
        $data['spt'] = $this->sptModel->find($id);

        if (!$data['spt']) {
            return redirect()->to('/spt')->with('error', 'SPT tidak ditemukan.');
        }

        $data['pelaksana_list'] = $this->sptPelaksanaModel->getPelaksanaBySpt($id);

        return view('spt/detail', $data);
    }
}
