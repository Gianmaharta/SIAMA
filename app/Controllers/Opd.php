<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OpdModel;

class Opd extends BaseController
{
    protected $opdModel;

    public function __construct()
    {
        $this->opdModel = new OpdModel();
    }

    public function index()
    {
        $data['opds'] = $this->opdModel->findAll();
        return view('opd/index', $data);
    }

    public function create()
    {
        return view('opd/create');
    }

    public function store()
    {
        $data = [
            'kode_opd'         => $this->request->getPost('kode_opd'),
            'nama_opd'         => $this->request->getPost('nama_opd'),
            'kuota_storage_mb' => $this->request->getPost('kuota_storage_mb') ?: 1000,
            'is_active'        => $this->request->getPost('is_active') ?? 1
        ];

        if (!$this->opdModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data OPD.');
        }

        return redirect()->to('/opd')->with('success', 'Data OPD berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data['opd'] = $this->opdModel->find($id);

        if (!$data['opd']) {
            return redirect()->to('/opd')->with('error', 'Data OPD tidak ditemukan.');
        }

        return view('opd/edit', $data);
    }

    public function update($id)
    {
        $data = [
            'id_opd'           => $id,
            'kode_opd'         => $this->request->getPost('kode_opd'),
            'nama_opd'         => $this->request->getPost('nama_opd'),
            'kuota_storage_mb' => $this->request->getPost('kuota_storage_mb'),
            'is_active'        => $this->request->getPost('is_active') ?? 0
        ];

        if (!$this->opdModel->save($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data OPD.');
        }

        return redirect()->to('/opd')->with('success', 'Data OPD berhasil diperbarui.');
    }

    public function delete($id)
    {
        try {
            $this->opdModel->delete($id);
            return redirect()->to('/opd')->with('success', 'Data OPD berhasil dihapus.');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Tangkap error jika ada constraints foreign key (RESTRICT)
            return redirect()->to('/opd')->with('error', 'Gagal menghapus OPD. Pastikan OPD ini tidak memiliki relasi data (seperti Bidang, User, atau Arsip).');
        }
    }
}
