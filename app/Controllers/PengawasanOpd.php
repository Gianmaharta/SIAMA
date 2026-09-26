<?php

namespace App\Controllers;

use App\Models\OpdModel;

/**
 * Dashboard Pengawasan Capaian Alih Media Seluruh OPD
 * Eksklusif untuk Admin Pemkab (LKD / Dinas Kearsipan).
 */
class PengawasanOpd extends BaseController
{
    protected OpdModel $opdModel;

    public function __construct()
    {
        $this->opdModel = new OpdModel();
    }

    /**
     * Menampilkan tabel capaian alih media per OPD.
     * Kolom: Nama OPD | Total SPT | Target Berkas | Berkas Selesai | Persentase | Status Kinerja
     */
    public function index()
    {
        $db   = \Config\Database::connect();
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Ambil semua OPD
        $opdList = $this->opdModel->orderBy('nama_opd', 'ASC')->findAll();

        $data_pengawasan = [];

        foreach ($opdList as $opd) {
            $idOpd = $opd['id_opd'];

            // Total SPT Terbit di OPD ini pada tahun yang dipilih
            $totalSpt = $db->table('spt')
                ->where('id_opd', $idOpd)
                ->where('YEAR(tanggal_spt)', $tahun)
                ->countAllResults(false);

            // Total Berkas Target Alih Media (semua arsip milik OPD ini pada tahun yang dipilih)
            $totalBerkas = $db->table('arsip')
                ->where('id_opd', $idOpd)
                ->where('YEAR(created_at)', $tahun)
                ->countAllResults(false);

            // Berkas yang sudah selesai dialihmediakan (status_verifikasi = 'Disetujui' atau sudah masuk BA)
            $berkasSelesai = $db->table('arsip')
                ->where('id_opd', $idOpd)
                ->where('YEAR(created_at)', $tahun)
                ->whereIn('status_verifikasi', ['Disetujui', 'Selesai'])
                ->countAllResults(false);

            // Hitung persentase capaian
            $persentase = $totalBerkas > 0
                ? round(($berkasSelesai / $totalBerkas) * 100, 1)
                : 0;

            // Tentukan status kinerja
            if ($persentase > 80) {
                $statusKinerja = 'Sangat Baik';
                $statusColor   = '#27ae60';
            } elseif ($persentase >= 50) {
                $statusKinerja = 'Baik';
                $statusColor   = '#f39c12';
            } else {
                $statusKinerja = 'Kurang';
                $statusColor   = '#e74c3c';
            }

            $data_pengawasan[] = [
                'nama_opd'       => $opd['nama_opd'],
                'total_spt'      => $totalSpt,
                'total_berkas'   => $totalBerkas,
                'berkas_selesai' => $berkasSelesai,
                'persentase'     => $persentase,
                'status_kinerja' => $statusKinerja,
                'status_color'   => $statusColor,
            ];
        }

        // Generate daftar tahun untuk filter (5 tahun terakhir)
        $tahunList = [];
        $currentYear = (int) date('Y');
        for ($i = $currentYear; $i >= $currentYear - 4; $i--) {
            $tahunList[] = $i;
        }

        return view('pengawasan/index', [
            'data_pengawasan' => $data_pengawasan,
            'tahun'           => $tahun,
            'tahun_list'      => $tahunList,
        ]);
    }
}
