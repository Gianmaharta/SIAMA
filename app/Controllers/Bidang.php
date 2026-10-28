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
        helper('logger');
    }

    public function index()
    {
        $session = session();
        $nama_role = $session->get('nama_role');
        $id_opd  = $session->get('id_opd');

        // Jika Admin_OPD, tampilkan hanya bidang di OPD-nya
        if ($nama_role === 'Admin_OPD') {
            $data['bidangs'] = $this->bidangModel->getBidangWithOpd($id_opd);
            $data['opds'] = $this->opdModel->where('id_opd', $id_opd)->findAll();
            $data['id_opd_locked'] = $id_opd;
        } else {
            // Jika Admin_Pemkab, tampilkan semua
            $data['bidangs'] = $this->bidangModel->getBidangWithOpd();
            $data['opds'] = $this->opdModel->orderBy('nama_opd', 'ASC')->findAll();
            $data['id_opd_locked'] = null;
        }

        return view('bidang/index', $data);
    }

    public function create()
    {
        $session = session();
        $nama_role = $session->get('nama_role');
        $id_opd  = $session->get('id_opd');

        if ($nama_role === 'Admin_OPD') {
            // Kunci pada OPD user tersebut
            $data['opds'] = $this->opdModel->where('id_opd', $id_opd)->findAll();
            $data['id_opd_locked'] = $id_opd;
        } else {
            // Bebas pilih OPD
            $data['opds'] = $this->opdModel->orderBy('nama_opd', 'ASC')->findAll();
            $data['id_opd_locked'] = null;
        }

        return view('bidang/create', $data);
    }

    public function store()
    {
        $session = session();
        $nama_role = $session->get('nama_role');
        $id_opd  = $session->get('id_opd');

        $selectedOpd = ($nama_role === 'Admin_OPD') ? $id_opd : $this->request->getPost('id_opd');
        $namaBidang = trim((string) $this->request->getPost('nama_bidang'));

        $data = [
            'id_opd'      => $selectedOpd,
            'nama_bidang' => $namaBidang
        ];

        if (!$this->bidangModel->insert($data)) {
            $errors = $this->bidangModel->errors();
            $errMsg = !empty($errors) ? implode(' ', $errors) : 'Gagal menyimpan data Bidang.';
            return redirect()->back()->withInput()->with('error', $errMsg);
        }

        log_activity('Bidang', 'CREATE', "Menambahkan data master Bidang baru: {$namaBidang}");

        return redirect()->to('/bidang')->with('success', 'Data Bidang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data['bidang'] = $this->bidangModel->find($id);

        if (!$data['bidang']) {
            return redirect()->to('/bidang')->with('error', 'Data Bidang tidak ditemukan.');
        }

        $session = session();
        $nama_role = $session->get('nama_role');
        $id_opd  = $session->get('id_opd');

        // Proteksi agar Admin_OPD tidak mengedit bidang di OPD lain
        if ($nama_role === 'Admin_OPD' && $data['bidang']['id_opd'] != $id_opd) {
            return redirect()->to('/bidang')->with('error', 'Akses ditolak.');
        }

        if ($nama_role === 'Admin_OPD') {
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
        // Pengecekan sekuriti
        $bidangLama = $this->bidangModel->find($id);
        if (!$bidangLama) {
            return redirect()->to('/bidang')->with('error', 'Data Bidang tidak ditemukan.');
        }

        $session = session();
        $nama_role = $session->get('nama_role');
        $id_opd  = $session->get('id_opd');

        if ($nama_role === 'Admin_OPD' && $bidangLama['id_opd'] != $id_opd) {
             return redirect()->to('/bidang')->with('error', 'Akses ditolak.');
        }

        $selectedOpd = ($nama_role === 'Admin_OPD') ? $id_opd : $this->request->getPost('id_opd');
        $namaBidang = trim((string) $this->request->getPost('nama_bidang'));

        $data = [
            'id_bidang'   => $id,
            'id_opd'      => $selectedOpd,
            'nama_bidang' => $namaBidang
        ];

        if (!$this->bidangModel->save($data)) {
            $errors = $this->bidangModel->errors();
            $errMsg = !empty($errors) ? implode(' ', $errors) : 'Gagal memperbarui data Bidang.';
            return redirect()->back()->withInput()->with('error', $errMsg);
        }

        log_activity('Bidang', 'UPDATE', "Memperbarui data master Bidang: {$namaBidang}", $bidangLama, $data);

        return redirect()->to('/bidang')->with('success', 'Data Bidang berhasil diperbarui.');
    }

    public function delete($id)
    {
        $bidangLama = $this->bidangModel->find($id);
        if (!$bidangLama) {
            return redirect()->to('/bidang')->with('error', 'Data Bidang tidak ditemukan.');
        }

        $session = session();
        $nama_role = $session->get('nama_role');
        $id_opd  = $session->get('id_opd');

        if ($nama_role === 'Admin_OPD' && $bidangLama['id_opd'] != $id_opd) {
             return redirect()->to('/bidang')->with('error', 'Akses ditolak.');
        }

        try {
            $this->bidangModel->delete($id);
            log_activity('Bidang', 'DELETE', "Menghapus data master Bidang: {$bidangLama['nama_bidang']}");
            return redirect()->to('/bidang')->with('success', 'Data Bidang berhasil dihapus.');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/bidang')->with('error', 'Gagal menghapus Bidang. Pastikan Bidang ini tidak memiliki relasi data (seperti Arsip atau User).');
        }
    }
}
