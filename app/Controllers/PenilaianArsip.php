<?php

namespace App\Controllers;

use App\Models\ArsipModel;

class PenilaianArsip extends BaseController
{
    protected $arsipModel;

    public function __construct()
    {
        $this->arsipModel = new ArsipModel();
        helper('logger');
    }

    public function index()
    {
        // Status filter: semua, belum, sudah
        $filter = $this->request->getGet('status') ?? 'semua';

        if ($filter == 'belum') {
            $data['arsip'] = $this->arsipModel->getArsipBelumDinilai();
        } elseif ($filter == 'sudah') {
            $data['arsip'] = $this->arsipModel->getArsipSudahDinilai();
        } else {
            // Kita butuh getArsipWithRelations tanpa parameter untuk mendapatkan semua arsip (admin pemkab role)
            $data['arsip'] = $this->arsipModel->getArsipWithRelations();
        }
        
        // Manual relations mapping for 'belum' and 'sudah' methods
        // Since getArsipBelumDinilai and getArsipSudahDinilai only use findAll(), they don't have relations.
        // It's better to fetch all with relations and then filter, or update the model methods. 
        // For simplicity and to reuse the join logic, we'll fetch all and filter in PHP or rewrite the query here.
        // Actually, getArsipWithRelations() can fetch all. We can just filter the array.
        $semuaArsip = $this->arsipModel->getArsipWithRelations();
        
        if ($filter == 'belum') {
            $data['arsip'] = array_filter($semuaArsip, function($item) {
                return $item['status_autentikasi'] === 'Belum Dinilai';
            });
        } elseif ($filter == 'sudah') {
            $data['arsip'] = array_filter($semuaArsip, function($item) {
                return $item['status_autentikasi'] !== 'Belum Dinilai';
            });
        } else {
            $data['arsip'] = $semuaArsip;
        }

        $data['filter'] = $filter;

        return view('penilaian/index', $data);
    }

    public function form($id_arsip)
    {
        $arsip = $this->arsipModel->getArsipWithRelations($id_arsip);

        if (!$arsip) {
            return redirect()->to('/penilaian')->with('error', 'Data arsip tidak ditemukan.');
        }

        $data['arsip'] = $arsip;
        return view('penilaian/form', $data);
    }

    public function store($id_arsip)
    {
        $arsip = $this->arsipModel->find($id_arsip);

        if (!$arsip) {
            return redirect()->to('/penilaian')->with('error', 'Data arsip tidak ditemukan.');
        }

        $skor_prioritas     = $this->request->getPost('skor_prioritas');
        $status_autentikasi = $this->request->getPost('status_autentikasi');
        $catatan_penilaian  = $this->request->getPost('catatan_penilaian');
        
        // Basic Validation
        if (!is_numeric($skor_prioritas) || $skor_prioritas < 0 || $skor_prioritas > 100) {
            return redirect()->back()->with('error', 'Skor prioritas harus berupa angka antara 0 hingga 100.');
        }

        if (empty($status_autentikasi)) {
            return redirect()->back()->with('error', 'Status autentikasi wajib dipilih.');
        }

        $dataPenilaian = [
            'skor_prioritas'     => $skor_prioritas,
            'status_autentikasi' => $status_autentikasi,
            'catatan_penilaian'  => $catatan_penilaian,
            'id_penilai'         => session()->get('user_id'),
        ];

        // Save
        $this->arsipModel->simpanPenilaian($id_arsip, $dataPenilaian);

        // Log Activity
        log_activity('Penilaian Arsip', 'update', "Memberikan penilaian (Skor: $skor_prioritas) pada Arsip: " . $arsip['nomor_arsip']);

        return redirect()->to('/penilaian')->with('success', 'Penilaian arsip berhasil disimpan.');
    }

    public function detail($id_arsip)
    {
        $arsip = $this->arsipModel->getArsipWithRelations($id_arsip);

        if (!$arsip) {
            return redirect()->to('/penilaian')->with('error', 'Data arsip tidak ditemukan.');
        }

        // Ambil nama penilai (Admin Pemkab yang menilai)
        $userModel = new \App\Models\UserModel();
        if ($arsip['id_penilai']) {
            $penilai = $userModel->find($arsip['id_penilai']);
            $arsip['nama_penilai'] = $penilai ? $penilai['nama'] : 'Tidak Diketahui';
        } else {
            $arsip['nama_penilai'] = '-';
        }

        $data['arsip'] = $arsip;
        return view('penilaian/detail', $data);
    }
}
