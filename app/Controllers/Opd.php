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
        helper('logger');
    }

    public function index()
    {
        $opds = $this->opdModel->orderBy('kode_opd', 'ASC')->findAll();
        
        $totalOpd = count($opds);
        $activeOpd = count(array_filter($opds, fn($item) => (int)($item['is_active'] ?? 0) === 1));
        $inactiveOpd = count(array_filter($opds, fn($item) => (int)($item['is_active'] ?? 0) === 0));
        $nextKodeOpd = $this->opdModel->generateNextKodeOpd();

        $data = [
            'opds'        => $opds,
            'totalOpd'    => $totalOpd,
            'activeOpd'   => $activeOpd,
            'inactiveOpd' => $inactiveOpd,
            'nextKodeOpd' => $nextKodeOpd,
        ];
        return view('opd/index', $data);
    }

    public function create()
    {
        $data['nextKodeOpd'] = $this->opdModel->generateNextKodeOpd();
        return view('opd/create', $data);
    }

    public function store()
    {
        $kodeOpd = trim($this->request->getPost('kode_opd') ?? '');
        $namaOpd = trim($this->request->getPost('nama_opd') ?? '');
        $kuota   = (int)($this->request->getPost('kuota_storage_mb') ?: 1000);
        $active  = (int)($this->request->getPost('is_active') ?? 1);

        if (empty($namaOpd)) {
            return redirect()->back()->withInput()->with('error', 'Nama OPD wajib diisi.');
        }

        // Auto-generate kode_opd dari sistem
        if (empty($kodeOpd)) {
            $kodeOpd = $this->opdModel->generateNextKodeOpd();
        } else {
            $existing = $this->opdModel->where('kode_opd', $kodeOpd)->first();
            if ($existing) {
                $kodeOpd = $this->opdModel->generateNextKodeOpd();
            }
        }

        $data = [
            'kode_opd'         => $kodeOpd,
            'nama_opd'         => $namaOpd,
            'kuota_storage_mb' => $kuota,
            'is_active'        => $active,
        ];

        if (!$this->opdModel->insert($data)) {
            $errors = $this->opdModel->errors();
            $msg = !empty($errors) ? implode(', ', $errors) : 'Gagal menyimpan data OPD.';
            return redirect()->back()->withInput()->with('error', $msg);
        }

        log_activity('OPD', 'CREATE', "Menambahkan data master OPD baru: {$namaOpd} ({$kodeOpd})");

        return redirect()->to('/opd')->with('success', "Data OPD '{$namaOpd}' ({$kodeOpd}) berhasil ditambahkan.");
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
        $opd = $this->opdModel->find($id);
        if (!$opd) {
            return redirect()->to('/opd')->with('error', 'Data OPD tidak ditemukan.');
        }

        $kodeOpd = trim($this->request->getPost('kode_opd') ?? '');
        $namaOpd = trim($this->request->getPost('nama_opd') ?? '');
        $kuota   = (int)($this->request->getPost('kuota_storage_mb') ?: 1000);
        $active  = (int)($this->request->getPost('is_active') ?? 0);

        if (empty($kodeOpd) || empty($namaOpd)) {
            return redirect()->back()->withInput()->with('error', 'Kode OPD dan Nama OPD wajib diisi.');
        }

        $duplicate = $this->opdModel->where('kode_opd', $kodeOpd)->where('id_opd !=', $id)->first();
        if ($duplicate) {
            return redirect()->back()->withInput()->with('error', "Kode OPD '{$kodeOpd}' sudah digunakan oleh OPD lain.");
        }

        $data = [
            'id_opd'           => $id,
            'kode_opd'         => $kodeOpd,
            'nama_opd'         => $namaOpd,
            'kuota_storage_mb' => $kuota,
            'is_active'        => $active,
        ];

        if (!$this->opdModel->save($data)) {
            $errors = $this->opdModel->errors();
            $msg = !empty($errors) ? implode(', ', $errors) : 'Gagal memperbarui data OPD.';
            return redirect()->back()->withInput()->with('error', $msg);
        }

        log_activity('OPD', 'UPDATE', "Memperbarui data master OPD: {$namaOpd} ({$kodeOpd})", $opd, $data);

        return redirect()->to('/opd')->with('success', "Data OPD '{$namaOpd}' berhasil diperbarui.");
    }

    public function delete($id)
    {
        $opd = $this->opdModel->find($id);
        if (!$opd) {
            return redirect()->to('/opd')->with('error', 'Data OPD tidak ditemukan.');
        }

        try {
            $this->opdModel->delete($id);
            log_activity('OPD', 'DELETE', "Menghapus data master OPD: {$opd['nama_opd']} ({$opd['kode_opd']})");
            return redirect()->to('/opd')->with('success', "Data OPD '{$opd['nama_opd']}' berhasil dihapus.");
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/opd')->with('error', 'Gagal menghapus OPD. Pastikan OPD ini tidak memiliki relasi data (seperti Bidang, User, atau Arsip).');
        } catch (\Exception $e) {
            return redirect()->to('/opd')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

