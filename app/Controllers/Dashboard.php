<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ArsipModel;
use App\Models\SptModel;
use App\Models\BeritaAcaraModel;
use App\Models\ActivityLogModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();
        
        // Ambil data sesi
        $data['user_id']      = $session->get('user_id');
        $data['id_role']      = $session->get('id_role');
        $data['nama_role']    = $session->get('nama_role');
        $data['id_opd']       = $session->get('id_opd');
        $data['id_bidang']    = $session->get('id_bidang'); // Bisa null jika Admin Pemkab / Pimpinan / Admin OPD
        $data['nama_lengkap'] = $session->get('nama_lengkap');

        // Inisialisasi Model
        $userModel        = new UserModel();
        $arsipModel       = new ArsipModel();
        $sptModel         = new SptModel();
        $beritaAcaraModel = new BeritaAcaraModel();
        $db = \Config\Database::connect();

        $stats = [];

        // Logika penarikan data sesuai nama_role (karena id_role sekarang adalah UUID)
        switch ($data['nama_role']) {
            case 'Admin_Pemkab':
                $stats['total_opd']          = $db->table('opd')->countAllResults();
                $stats['total_users']        = $userModel->countAllResults();
                $stats['total_arsip']        = $arsipModel->countAllGlobal();
                $stats['total_spt']          = $sptModel->countAllGlobal();
                $stats['total_berita_acara'] = $beritaAcaraModel->countAllGlobal();
                break;

            case 'Pimpinan':
                $stats['spt_diterbitkan']      = $sptModel->countAllGlobal(); // Atau spesifik id_pimpinan jika diperlukan
                $stats['ba_menanti_pimpinan']  = $beritaAcaraModel->countMenungguTtdPimpinan($data['user_id']);
                break;

            case 'Admin_OPD':
                $stats['total_users_opd']   = $userModel->where('id_opd', $data['id_opd'])->countAllResults();
                $stats['total_bidang_opd']  = $db->table('bidang')->where('id_opd', $data['id_opd'])->countAllResults();
                $stats['total_arsip_opd']   = $arsipModel->countByOpd($data['id_opd']);
                break;

            case 'Kepala_Bidang':
                if ($data['id_bidang']) {
                    $stats['total_arsip_bidang'] = $arsipModel->countByBidang($data['id_bidang']);
                } else {
                    $stats['total_arsip_bidang'] = 0;
                }
                $stats['ba_menanti_kabid']   = $beritaAcaraModel->countMenungguVerifikasiKabid($data['user_id']);
                break;

            case 'Arsiparis':
                $stats['arsip_diunggah']     = $arsipModel->where('created_by', $data['user_id'])->countAllResults();
                $stats['penugasan_spt']      = $sptModel->countByPelaksana($data['user_id']);
                break;
        }

        $data['stats'] = $stats;

        // Data Grafik: Distribusi Arsip Per OPD
        $chartQuery = $db->table('opd o')
            ->select('o.nama_opd, o.kode_opd, COUNT(a.id_arsip) AS total_arsip')
            ->join('arsip a', 'a.id_opd = o.id_opd', 'left')
            ->groupBy('o.id_opd, o.nama_opd, o.kode_opd')
            ->orderBy('total_arsip', 'DESC')
            ->limit(6)
            ->get()
            ->getResultArray();

        if (!empty($chartQuery)) {
            $data['chart_labels'] = array_map(function($item) {
                // Sederhanakan nama label (contoh: "Dinas Komunikasi..." -> "Kominfo" atau potongan nama)
                $nama = $item['nama_opd'];
                if (stripos($nama, 'Komunikasi') !== false) return 'Kominfo';
                if (stripos($nama, 'Pendidikan') !== false) return 'Disdik';
                if (stripos($nama, 'Arsip') !== false) return 'Arsip';
                if (stripos($nama, 'Kesehatan') !== false) return 'Dinkes';
                if (stripos($nama, 'Pertanian') !== false) return 'Dispertan';
                return strlen($nama) > 10 ? substr($nama, 0, 8) . '..' : $nama;
            }, $chartQuery);
            $data['chart_values'] = array_map('intval', array_column($chartQuery, 'total_arsip'));
        } else {
            $data['chart_labels'] = ['Kominfo', 'Disdik', 'Arsip', 'Dinkes'];
            $data['chart_values'] = [0, 0, 0, 0];
        }

        // Data Aktivitas Terbaru
        $activityLogModel = new ActivityLogModel();
        $recentActivities = $activityLogModel->getActivityLogsWithFilter()
            ->limit(5)
            ->get()
            ->getResultArray();

        $data['recent_activities'] = $recentActivities;

        return view('dashboard/index', $data);
    }
}
