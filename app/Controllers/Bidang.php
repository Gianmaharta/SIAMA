<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BidangModel;
use App\Models\OpdModel;

class Bidang extends BaseController
{
    protected $bidangModel;
    protected $opdModel;

    public function __construct()
    {
        $this->bidangModel = new BidangModel();
        $this->opdModel = new OpdModel();
    }

    public function index()
    {
        $session = session();
        $id_role = $session->get('id_role');
        $id_opd  = $session->get('id_opd');

        // Jika Admin_OPD (role 3), tampilkan hanya bidang di OPD-nya
        if ($id_role == 3) {
            $data['bidangs'] = $this->bidangModel->getBidangWithOpd($id_opd);
        } else {
            // Jika Admin_Pemkab (role 1), tampilkan semua
            $data['bidangs'] = $this->bidangModel->getBidangWithOpd();
        }

        return view('bidang/index', $data);
    }

    public function create()
    {
        $session = session();
        $id_role = $session->get('id_role');
        $id_opd  = $session->get('id_opd');

        if ($id_role == 3) {
            // Kunci pada OPD user tersebut
            $data['opds'] = $this->opdModel->where('id_opd', $id_opd)->findAll();
            $data['id_opd_locked'] = $id_opd;
        } else {
            // Bebas pilih OPD
            $data['opds'] = $this->opdModel->findAll();
            $data['id_opd_locked'] = null;
        }

        return view('bidang/create', $data);
    }

    public function store()
    {
        $data = [
            'id_opd'      => $this->request->getPost('id_opd'),
            'nama_bidang' => $this->request->getPost('nama_bidang')
        ];

        if (!$this->bidangModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data Bidang.');
        }

        return redirect()->to('/bidang')->with('success', 'Data Bidang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data['bidang'] = $this->bidangModel->find($id);

        if (!$data['bidang']) {
            return redirect()->to('/bidang')->with('error', 'Data Bidang tidak ditemukan.');
        }

        $session = session();
        $id_role = $session->get('id_role');
        $id_opd  = $session->get('id_opd');

        // Proteksi agar Admin_OPD tidak mengedit bidang di OPD lain
        if ($id_role == 3 && $data['bidang']['id_opd'] != $id_opd) {
            return redirect()->to('/bidang')->with('error', 'Akses ditolak.');
        }

        if ($id_role == 3) {
            $data['opds'] = $this->opdModel->where('id_opd', $id_opd)->findAll();
            $data['id_opd_locked'] = $id_opd;
        } else {
            $data['opds'] = $this->opdModel->findAll();
            $data['id_opd_locked'] = null;
        }

        return view('bidang/edit', $data);
    }

    public function update($id)
    {
        // Pengecekan sekuriti sederhana
        $bidangLama = $this->bidangModel->find($id);
        if (!$bidangLama) {
            return redirect()->to('/bidang')->with('error', 'Data Bidang tidak ditemukan.');
        }

        $session = session();
        $id_role = $session->get('id_role');
        $id_opd  = $session->get('id_opd');

        if ($id_role == 3 && $bidangLama['id_opd'] != $id_opd) {
             return redirect()->to('/bidang')->with('error', 'Akses ditolak.');
        }

        $data = [
            'id_bidang'   => $id,
            'id_opd'      => $this->request->getPost('id_opd'),
            'nama_bidang' => $this->request->getPost('nama_bidang')
        ];

        if (!$this->bidangModel->save($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data Bidang.');
        }

        return redirect()->to('/bidang')->with('success', 'Data Bidang berhasil diperbarui.');
    }

    public function delete($id)
    {
        $bidangLama = $this->bidangModel->find($id);
        if (!$bidangLama) {
            return redirect()->to('/bidang')->with('error', 'Data Bidang tidak ditemukan.');
        }

        $session = session();
        $id_role = $session->get('id_role');
        $id_opd  = $session->get('id_opd');

        if ($id_role == 3 && $bidangLama['id_opd'] != $id_opd) {
             return redirect()->to('/bidang')->with('error', 'Akses ditolak.');
        }

        try {
            $this->bidangModel->delete($id);
            return redirect()->to('/bidang')->with('success', 'Data Bidang berhasil dihapus.');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/bidang')->with('error', 'Gagal menghapus Bidang. Pastikan Bidang ini tidak memiliki relasi data (seperti Arsip atau User).');
        }
    }
}
