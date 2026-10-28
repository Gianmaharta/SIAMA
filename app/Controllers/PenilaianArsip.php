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
        $filter = $this->request->getGet('status') ?? 'semua';
        $semuaArsip = $this->arsipModel->getArsipWithRelations();

        $totalArsip = count($semuaArsip);
        $belumDinilai = 0;
        $sudahDinilai = 0;
        $revisiMetadata = 0;
        $ditolak = 0;

        foreach ($semuaArsip as $row) {
            $status = trim($row['status_autentikasi'] ?? '');
            if ($status === 'Belum Dinilai' || empty($status)) {
                $belumDinilai++;
            } elseif ($status === 'Revisi Metadata' || $status === 'Perlu Revisi') {
                $revisiMetadata++;
            } elseif ($status === 'Ditolak') {
                $ditolak++;
            } else {
                // Lolos Evaluasi / Autentik / Sudah Dinilai
                $sudahDinilai++;
            }
        }

        if ($filter == 'belum') {
            $filteredArsip = array_values(array_filter($semuaArsip, function($item) {
                return ($item['status_autentikasi'] ?? '') === 'Belum Dinilai';
            }));
        } elseif ($filter == 'sudah') {
            $filteredArsip = array_values(array_filter($semuaArsip, function($item) {
                return ($item['status_autentikasi'] ?? '') !== 'Belum Dinilai';
            }));
        } else {
            $filteredArsip = $semuaArsip;
        }

        $data = [
            'arsip'          => $filteredArsip,
            'semuaArsip'     => $semuaArsip,
            'totalArsip'     => $totalArsip,
            'belumDinilai'   => $belumDinilai,
            'sudahDinilai'   => $sudahDinilai,
            'revisiMetadata' => $revisiMetadata,
            'ditolak'        => $ditolak,
            'filter'         => $filter,
        ];

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

        // Log Activity (hanya tercatat jika nilai / status berubah)
        log_activity('Penilaian Arsip', 'UPDATE', "Memberikan penilaian (Skor: $skor_prioritas) pada Arsip: " . $arsip['nomor_arsip'], $arsip, $dataPenilaian);

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
