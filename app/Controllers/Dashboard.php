<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ArsipModel;
use App\Models\SptModel;
use App\Models\BeritaAcaraModel;

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
        // (Opsional jika butuh count OPD/Bidang secara spesifik, namun tidak diminta secara eksplisit, 
        // Admin_OPD butuh count Bidang di OPD tersebut, kita gunakan UserModel atau query builder sederhana)
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

        return view('dashboard/index', $data);
    }
}
