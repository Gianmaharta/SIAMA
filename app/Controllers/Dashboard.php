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
        $data['id_bidang']    = $session->get('id_bidang');
        $data['nama_lengkap'] = $session->get('nama_lengkap');

        // Inisialisasi Model
        $userModel        = new UserModel();
        $arsipModel       = new ArsipModel();
        $sptModel         = new SptModel();
        $beritaAcaraModel = new BeritaAcaraModel();
        $db               = \Config\Database::connect();

        // Fallback: Jika id_opd atau id_bidang kosong di sesi namun user_id ada, ambil dari database users
        if (!empty($data['user_id'])) {
            $userDb = $userModel->find($data['user_id']);
            if ($userDb) {
                if (empty($data['id_opd']) && !empty($userDb['id_opd'])) {
                    $data['id_opd'] = $userDb['id_opd'];
                    $session->set('id_opd', $userDb['id_opd']);
                }
                if (empty($data['id_bidang']) && !empty($userDb['id_bidang'])) {
                    $data['id_bidang'] = $userDb['id_bidang'];
                    $session->set('id_bidang', $userDb['id_bidang']);
                }
                if (empty($data['nama_lengkap']) && !empty($userDb['nama'])) {
                    $data['nama_lengkap'] = $userDb['nama'];
                    $session->set('nama_lengkap', $userDb['nama']);
                }
            }
        }

        // Ambil nama OPD dan nama Bidang dari database jika tersedia
        $data['nama_opd']    = null;
        $data['nama_bidang'] = null;
        if (!empty($data['id_opd'])) {
            $opdRow = $db->table('opd')->select('nama_opd, kode_opd')->where('id_opd', $data['id_opd'])->get()->getRowArray();
            $data['nama_opd'] = $opdRow['nama_opd'] ?? null;
        }
        if (!empty($data['id_bidang'])) {
            $bidangRow = $db->table('bidang')->select('nama_bidang')->where('id_bidang', $data['id_bidang'])->get()->getRowArray();
            $data['nama_bidang'] = $bidangRow['nama_bidang'] ?? null;
        }

        $stats = [];
        $data['users_opd'] = [];
        $data['bidang_opd'] = [];
        $data['recent_arsip_opd'] = [];
        $data['recent_spt'] = [];
        $data['recent_ba'] = [];
        $data['pending_work_summary'] = [];
        $data['workflow_monitoring_list'] = [];
        $data['opd_progress_list'] = [];
        $data['ringkasan_alih_media'] = [
            'total_arsip'   => 0,
            'belum_selesai' => 0,
            'proses'        => 0,
            'menunggu'      => 0,
            'selesai'       => 0,
            'persentase'    => 0,
        ];
        $data['workflow_kabid_list'] = [];
        $data['action_items_kabid']  = [
            'spt_belum_ditugaskan' => 0,
            'arsip_menunggu'       => 0,
            'ba_menunggu'          => 0,
            'dokumen_revisi'       => 0,
            'total_action'         => 0,
        ];
        $data['ba_action_required']     = [];
        $data['pimpinan_spt_monitoring'] = [];
        $data['riwayat_ba']             = [];
        $data['ba_stats_pimpinan']       = [
            'semua'        => 0,
            'menunggu_ttd' => 0,
            'selesai'      => 0,
            'revisi'       => 0,
            'draf_kabid'   => 0,
        ];
        $data['pimpinan_activities']     = [];
        $data['spt_belum_dikerjakan']    = [];
        $data['monitoring_arsip_list']   = [];
        $data['status_arsip_arsiparis']  = [
            'selesai'     => 0,
            'proses'      => 0,
            'telaah'      => 0,
            'diproses'    => 0,
            'gagal'       => 0,
            'total'       => 0,
            'selesai_pct' => 0,
            'proses_pct'  => 0,
            'gagal_pct'   => 0,
        ];

        // Logika penarikan data sesuai nama_role
        switch ($data['nama_role']) {
            case 'Admin_Pemkab':
                $stats['total_opd']          = $db->table('opd')->countAllResults();
                $stats['total_users']        = $userModel->countAllResults();
                $stats['total_arsip']        = $arsipModel->countAllGlobal();
                $stats['total_spt']          = $sptModel->countAllGlobal();
                $stats['total_berita_acara'] = $beritaAcaraModel->countAllGlobal();

                $data['pending_work_summary']     = $this->getPendingWorkSummary();
                $data['workflow_monitoring_list'] = $this->getWorkflowMonitoring();
                $data['opd_progress_list']        = $this->getOpdProgress();
                break;

            case 'Pimpinan':
            case 'Kepala_Dinas':
            case 'Kepala Dinas':
                $idOpd  = $data['id_opd'] ?? null;
                $userId = $data['user_id'] ?? null;

                // 1. Total SPT Diterbitkan di OPD Pimpinan
                $sptQuery = $db->table('spt');
                if (!empty($idOpd)) {
                    $sptQuery->where('id_opd', $idOpd);
                } elseif (!empty($userId)) {
                    $sptQuery->where('id_pimpinan', $userId);
                }
                $stats['spt_diterbitkan'] = $sptQuery->countAllResults();

                // 2. Berita Acara Menanti TTD Pimpinan di OPD (Draf_Pimpinan & Menunggu_Pimpinan)
                $baQueryCount = $db->table('berita_acara')
                    ->whereIn('status_persetujuan', ['Draf_Pimpinan', 'Menunggu_Pimpinan']);
                if (!empty($idOpd)) {
                    $baQueryCount->where('id_opd', $idOpd);
                } elseif (!empty($userId)) {
                    $baQueryCount->where('id_pimpinan', $userId);
                }
                $stats['ba_menanti_pimpinan'] = $baQueryCount->countAllResults();

                // 3. Total Arsip Dialihmediakan di OPD Pimpinan
                $arsipQuery = $db->table('arsip');
                if (!empty($idOpd)) {
                    $arsipQuery->where('id_opd', $idOpd);
                }
                $stats['total_arsip_opd'] = $arsipQuery->countAllResults();

                // 4. Berita Acara Selesai di OPD Pimpinan
                $baSelesaiQuery = $db->table('berita_acara')->where('status_persetujuan', 'Selesai');
                if (!empty($idOpd)) {
                    $baSelesaiQuery->where('id_opd', $idOpd);
                } elseif (!empty($userId)) {
                    $baSelesaiQuery->where('id_pimpinan', $userId);
                }
                $stats['ba_selesai'] = $baSelesaiQuery->countAllResults();

                // 5. Dokumen Membutuhkan Tindakan Pimpinan (BA yang siap ditandatangani)
                $baActionReqQuery = $db->table('berita_acara ba')
                    ->select('ba.id_berita_acara, ba.nomor_ba, ba.tanggal_ba, ba.status_persetujuan, ba.catatan_revisi, ba.created_at, o.nama_opd, u_pelaksana.nama as nama_pelaksana, s.perihal as perihal_spt, s.nomor_spt')
                    ->join('opd o', 'o.id_opd = ba.id_opd', 'left')
                    ->join('users u_pelaksana', 'u_pelaksana.id_user = ba.id_pelaksana', 'left')
                    ->join('spt s', 's.id_spt = ba.id_spt', 'left')
                    ->whereIn('ba.status_persetujuan', ['Draf_Pimpinan', 'Menunggu_Pimpinan']);
                if (!empty($idOpd)) {
                    $baActionReqQuery->where('ba.id_opd', $idOpd);
                } elseif (!empty($userId)) {
                    $baActionReqQuery->where('ba.id_pimpinan', $userId);
                }
                $data['ba_action_required'] = $baActionReqQuery
                    ->orderBy('ba.created_at', 'DESC')
                    ->get()
                    ->getResultArray();

                // 6. Monitoring Pelaksanaan SPT untuk OPD Pimpinan
                $sptMonitoringQuery = $db->table('spt s')
                    ->select('s.id_spt, s.nomor_spt, s.tanggal_spt, s.perihal, s.status as status_spt, s.status_penugasan, s.created_at, s.updated_at, o.nama_opd')
                    ->join('opd o', 'o.id_opd = s.id_opd', 'left');
                if (!empty($idOpd)) {
                    $sptMonitoringQuery->where('s.id_opd', $idOpd);
                } elseif (!empty($userId)) {
                    $sptMonitoringQuery->where('s.id_pimpinan', $userId);
                }
                $rawSpts = $sptMonitoringQuery
                    ->orderBy('s.tanggal_spt', 'DESC')
                    ->orderBy('s.created_at', 'DESC')
                    ->limit(10)
                    ->get()
                    ->getResultArray();

                $pimpinanSptList = [];
                foreach ($rawSpts as $sptItem) {
                    $idSpt = $sptItem['id_spt'];

                    // Hitung jumlah arsip terkait SPT
                    $totalBerkas = $db->table('arsip')->where('id_spt', $idSpt)->countAllResults();

                    // Ambil BA terbaru terkait SPT
                    $baLatest = $db->table('berita_acara')
                        ->where('id_spt', $idSpt)
                        ->orderBy('created_at', 'DESC')
                        ->get()
                        ->getRowArray();

                    // Penentuan status pelaksanaan alur secara dinamis
                    if ($sptItem['status_spt'] === 'Selesai' || (!empty($baLatest) && $baLatest['status_persetujuan'] === 'Selesai')) {
                        $statusProses = 'Selesai';
                        $statusClass  = 'badge-status-green';
                    } elseif (!empty($baLatest) && in_array($baLatest['status_persetujuan'], ['Draf_Pimpinan', 'Menunggu_Pimpinan'])) {
                        $statusProses = 'Menunggu TTD Pimpinan';
                        $statusClass  = 'badge-status-purple';
                    } elseif (!empty($baLatest) && $baLatest['status_persetujuan'] === 'Draf_Kabid') {
                        $statusProses = 'Menunggu Verifikasi Kabid';
                        $statusClass  = 'badge-status-amber';
                    } elseif (!empty($baLatest) && $baLatest['status_persetujuan'] === 'Revisi') {
                        $statusProses = 'Perbaikan Draf (Revisi)';
                        $statusClass  = 'badge-status-rose';
                    } elseif ($sptItem['status_penugasan'] === 'ditugaskan') {
                        $menungguArsip = $db->table('arsip')
                            ->where('id_spt', $idSpt)
                            ->whereIn('status_verifikasi', ['Menunggu', 'Belum_Diproses'])
                            ->countAllResults();
                        if ($totalBerkas === 0) {
                            $statusProses = 'Menunggu Mulai Alih Media';
                            $statusClass  = 'badge-status-blue';
                        } elseif ($menungguArsip > 0) {
                            $statusProses = 'Menunggu Verifikasi Arsip';
                            $statusClass  = 'badge-status-amber';
                        } else {
                            $statusProses = 'Sedang Alih Media';
                            $statusClass  = 'badge-status-blue';
                        }
                    } else {
                        $statusProses = 'Menunggu Penugasan Arsiparis';
                        $statusClass  = 'badge-status-amber';
                    }

                    $sptItem['total_arsip']   = $totalBerkas;
                    $sptItem['status_proses'] = $statusProses;
                    $sptItem['status_class']  = $statusClass;
                    $pimpinanSptList[]        = $sptItem;
                }
                $data['pimpinan_spt_monitoring'] = $pimpinanSptList;
                $data['recent_spt']              = $pimpinanSptList; // kompatibilitas

                // 7. Riwayat Berita Acara untuk OPD Pimpinan
                $riwayatBaQuery = $db->table('berita_acara ba')
                    ->select('ba.id_berita_acara, ba.nomor_ba, ba.tanggal_ba, ba.status_persetujuan, ba.catatan_revisi, ba.created_at, ba.updated_at, o.nama_opd, u_pelaksana.nama as nama_pelaksana, s.perihal as perihal_spt, s.nomor_spt')
                    ->join('opd o', 'o.id_opd = ba.id_opd', 'left')
                    ->join('users u_pelaksana', 'u_pelaksana.id_user = ba.id_pelaksana', 'left')
                    ->join('spt s', 's.id_spt = ba.id_spt', 'left');
                if (!empty($idOpd)) {
                    $riwayatBaQuery->where('ba.id_opd', $idOpd);
                } elseif (!empty($userId)) {
                    $riwayatBaQuery->where('ba.id_pimpinan', $userId);
                }
                $riwayatBaList = $riwayatBaQuery
                    ->orderBy('ba.tanggal_ba', 'DESC')
                    ->orderBy('ba.created_at', 'DESC')
                    ->limit(20)
                    ->get()
                    ->getResultArray();
                $data['riwayat_ba'] = $riwayatBaList;
                $data['recent_ba']  = $riwayatBaList; // kompatibilitas

                $baStats = [
                    'semua'        => count($riwayatBaList),
                    'menunggu_ttd' => 0,
                    'selesai'      => 0,
                    'revisi'       => 0,
                    'draf_kabid'   => 0,
                ];
                foreach ($riwayatBaList as $b) {
                    if (in_array($b['status_persetujuan'], ['Draf_Pimpinan', 'Menunggu_Pimpinan'])) {
                        $baStats['menunggu_ttd']++;
                    } elseif ($b['status_persetujuan'] === 'Selesai') {
                        $baStats['selesai']++;
                    } elseif ($b['status_persetujuan'] === 'Revisi') {
                        $baStats['revisi']++;
                    } elseif ($b['status_persetujuan'] === 'Draf_Kabid') {
                        $baStats['draf_kabid']++;
                    }
                }
                $data['ba_stats_pimpinan'] = $baStats;
                break;

            case 'Admin_OPD':
            case 'Administrator OPD':
                $idOpd = $data['id_opd'] ?? null;
                if (!empty($idOpd)) {
                    $stats['total_users_opd']  = $userModel->where('id_opd', $idOpd)->countAllResults();
                    $stats['total_bidang_opd'] = $db->table('bidang')->where('id_opd', $idOpd)->countAllResults();
                    $stats['total_arsip_opd']  = $arsipModel->countByOpd($idOpd);

                    // 1. Ringkasan Proses Alih Media di Lingkungan OPD
                    $arsipStats = $db->table('arsip')
                        ->select("
                            COUNT(id_arsip) as total_arsip,
                            SUM(CASE WHEN status_verifikasi IN ('Selesai', 'Disetujui', 'Terverifikasi') THEN 1 ELSE 0 END) as selesai_count,
                            SUM(CASE WHEN status_verifikasi IN ('Draft', 'Proses', 'Dalam_Proses', 'Sedang Diproses') THEN 1 ELSE 0 END) as proses_count,
                            SUM(CASE WHEN status_verifikasi IN ('Menunggu', 'Belum_Diproses', 'Belum Diproses') THEN 1 ELSE 0 END) as menunggu_count
                        ")
                        ->where('id_opd', $idOpd)
                        ->get()
                        ->getRowArray();

                    $totalArsipOpd = (int)($arsipStats['total_arsip'] ?? 0);
                    $selesaiCount  = (int)($arsipStats['selesai_count'] ?? 0);
                    $prosesCount   = (int)($arsipStats['proses_count'] ?? 0);
                    $menungguCount = (int)($arsipStats['menunggu_count'] ?? 0);
                    $belumSelesai  = $totalArsipOpd - $selesaiCount;
                    $persenSelesai = $totalArsipOpd > 0 ? round(($selesaiCount / $totalArsipOpd) * 100, 1) : 0;

                    $data['ringkasan_alih_media'] = [
                        'total_arsip'   => $totalArsipOpd,
                        'belum_selesai' => $belumSelesai,
                        'proses'        => $prosesCount,
                        'menunggu'      => $menungguCount,
                        'selesai'       => $selesaiCount,
                        'persentase'    => $persenSelesai,
                    ];

                    // 2. Monitoring Workflow Pengarsipan Khusus OPD
                    $data['workflow_monitoring_list'] = $this->getWorkflowMonitoring($idOpd);

                    // 3. Ambil daftar pengguna di OPD
                    $data['users_opd'] = $db->table('users u')
                        ->select('u.id_user, u.nama, u.email, u.is_active, r.nama_role')
                        ->join('user_roles ur', 'ur.id_user = u.id_user', 'left')
                        ->join('roles r', 'r.id_role = ur.id_role', 'left')
                        ->where('u.id_opd', $idOpd)
                        ->orderBy('u.created_at', 'DESC')
                        ->limit(5)
                        ->get()
                        ->getResultArray();

                    // 4. Ambil daftar bidang di OPD beserta jumlah arsip
                    $data['bidang_opd'] = $db->table('bidang b')
                        ->select('b.id_bidang, b.nama_bidang, COUNT(a.id_arsip) AS total_arsip')
                        ->join('arsip a', 'a.id_bidang = b.id_bidang', 'left')
                        ->where('b.id_opd', $idOpd)
                        ->groupBy('b.id_bidang, b.nama_bidang')
                        ->orderBy('b.nama_bidang', 'ASC')
                        ->limit(5)
                        ->get()
                        ->getResultArray();

                    // 5. Ambil arsip terbaru di OPD beserta nomor SPT terkait
                    $data['recent_arsip_opd'] = $db->table('arsip a')
                        ->select('a.id_arsip, a.nomor_arsip, a.nama_arsip, a.created_at, a.status_verifikasi, a.id_spt, s.nomor_spt')
                        ->join('spt s', 's.id_spt = a.id_spt', 'left')
                        ->where('a.id_opd', $idOpd)
                        ->orderBy('a.created_at', 'DESC')
                        ->limit(6)
                        ->get()
                        ->getResultArray();
                } else {
                    $stats['total_users_opd']     = 0;
                    $stats['total_bidang_opd']    = 0;
                    $stats['total_arsip_opd']     = 0;
                    $data['ringkasan_alih_media'] = [
                        'total_arsip'   => 0,
                        'belum_selesai' => 0,
                        'proses'        => 0,
                        'menunggu'      => 0,
                        'selesai'       => 0,
                        'persentase'    => 0,
                    ];
                    $data['workflow_monitoring_list'] = [];
                    $data['users_opd']            = [];
                    $data['bidang_opd']           = [];
                    $data['recent_arsip_opd']     = [];
                }
                break;

            case 'Kepala_Bidang':
            case 'Kepala Bidang':
            case 'Kabid':
                $idBidang = $data['id_bidang'] ?? null;
                $userId   = $data['user_id'] ?? null;
                $idOpd    = $data['id_opd'] ?? null;

                // Builder pembantu: query arsip terfilter berdasarkan OPD dan Bidang Kepala Bidang
                $buildScopedArsip = function() use ($db, $idOpd, $idBidang) {
                    $builder = $db->table('arsip');
                    if (!empty($idOpd)) {
                        $builder->where('id_opd', $idOpd);
                    }
                    if (!empty($idBidang)) {
                        $builder->where('id_bidang', $idBidang);
                    }
                    return $builder;
                };

                // 1. Total Arsip di Bidang & OPD
                $totalArsip = (clone $buildScopedArsip())->countAllResults();
                $stats['total_arsip_bidang'] = $totalArsip;

                // Helper Builder untuk Berita Acara Menanti Verifikasi Kabid (terfilter strictly sesuai OPD & Bidang)
                $buildScopedBaKabid = function() use ($db, $idOpd, $idBidang, $userId) {
                    $builder = $db->table('berita_acara ba')
                        ->join('users u', 'u.id_user = ba.id_pelaksana', 'left')
                        ->where('ba.status_persetujuan', 'Draf_Kabid');

                    if (!empty($idOpd)) {
                        $builder->where('ba.id_opd', $idOpd);
                    }

                    if (!empty($idBidang)) {
                        $escapedBidang = $db->escape($idBidang);
                        $escapedUser   = $db->escape($userId);
                        $builder->where("(
                            ba.id_kabid = {$escapedUser}
                            OR u.id_bidang = {$escapedBidang}
                            OR EXISTS (
                                SELECT 1 FROM berita_acara_detail bad 
                                JOIN arsip a ON a.id_arsip = bad.id_arsip 
                                WHERE bad.id_berita_acara = ba.id_berita_acara AND a.id_bidang = {$escapedBidang}
                            )
                            OR (ba.id_kabid IS NULL AND ba.id_opd = " . $db->escape($idOpd) . ")
                        )");
                    } elseif (!empty($userId)) {
                        $escapedUser = $db->escape($userId);
                        $builder->where("(ba.id_kabid = {$escapedUser} OR ba.id_kabid IS NULL)");
                    }

                    return $builder;
                };

                // 2. Berita Acara Menanti Verifikasi Kabid
                $stats['ba_menanti_kabid'] = (clone $buildScopedBaKabid())->countAllResults();

                // 3. Distribusi Status Arsip (Untuk Donut Chart) dari Database
                $selesaiCount = (clone $buildScopedArsip())
                    ->whereIn('status_verifikasi', ['Selesai', 'Disetujui', 'Terverifikasi'])
                    ->countAllResults();

                $prosesCount = (clone $buildScopedArsip())
                    ->whereIn('status_verifikasi', ['Proses', 'Dalam_Proses', 'Sedang Diproses', 'Sedang_Diproses'])
                    ->countAllResults();

                $belumCount = (clone $buildScopedArsip())
                    ->whereIn('status_verifikasi', ['Menunggu', 'Belum_Diproses', 'Draft', 'Belum Diproses'])
                    ->countAllResults();

                $gagalCount = (clone $buildScopedArsip())
                    ->whereIn('status_verifikasi', ['Ditolak', 'Gagal', 'Dibatalkan'])
                    ->countAllResults();

                // Hitung sisa jika ada status lain agar total sinkron
                $sumKnown = $selesaiCount + $prosesCount + $belumCount + $gagalCount;
                if ($totalArsip > $sumKnown) {
                    $belumCount += ($totalArsip - $sumKnown);
                }

                $data['status_arsip_kabid'] = [
                    'selesai'        => $selesaiCount,
                    'dalam_proses'   => $prosesCount,
                    'belum_diproses' => $belumCount,
                    'gagal'          => $gagalCount,
                    'total'          => $totalArsip,
                ];

                // 4. Arsip per Bulan (Untuk Bar Chart) dari Database sesuai OPD & Bidang
                $yearRows = (clone $buildScopedArsip())
                    ->select('YEAR(created_at) as yr')
                    ->where('created_at IS NOT NULL')
                    ->distinct()
                    ->get()
                    ->getResultArray();

                $availableYears = array_filter(array_column($yearRows, 'yr'));
                if (!in_array('2026', $availableYears)) {
                    $availableYears[] = '2026';
                }
                $currYear = (string)date('Y');
                if (!in_array($currYear, $availableYears)) {
                    $availableYears[] = $currYear;
                }
                $availableYears = array_values(array_unique($availableYears));
                rsort($availableYears);

                $monthlyDataByYear = [];
                foreach ($availableYears as $yr) {
                    $monthlyRes = (clone $buildScopedArsip())
                        ->select('MONTH(created_at) as bln, COUNT(id_arsip) as total')
                        ->where('created_at IS NOT NULL')
                        ->where('YEAR(created_at)', $yr)
                        ->groupBy('MONTH(created_at)')
                        ->get()
                        ->getResultArray();

                    $months = array_fill(1, 12, 0);
                    foreach ($monthlyRes as $m) {
                        $bln = (int)$m['bln'];
                        if ($bln >= 1 && $bln <= 12) {
                            $months[$bln] = (int)$m['total'];
                        }
                    }
                    $monthlyDataByYear[$yr] = array_values($months);
                }

                $selectedYear = in_array('2026', $availableYears) ? '2026' : (string)($availableYears[0] ?? date('Y'));
                $data['kabid_available_years'] = $availableYears;
                $data['kabid_monthly_data']    = $monthlyDataByYear;
                $data['selected_year']         = $selectedYear;
                $data['monthly_arsip_kabid']   = [
                    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    'values' => $monthlyDataByYear[$selectedYear] ?? array_fill(0, 12, 0),
                    'tahun'  => $selectedYear
                ];

                // 5. Berita Acara Menanti Verifikasi Kabid (Tabel)
                $data['ba_menanti_list'] = (clone $buildScopedBaKabid())
                    ->select('ba.id_berita_acara, ba.nomor_ba, ba.tanggal_ba, ba.status_persetujuan, s.nomor_spt, u.nama as nama_pelaksana')
                    ->join('spt s', 's.id_spt = ba.id_spt', 'left')
                    ->orderBy('ba.created_at', 'DESC')
                    ->limit(10)
                    ->get()
                    ->getResultArray();

                // 6. Ringkasan Pekerjaan yang Membutuhkan Tindakan Kabid (Prioritas Aksi)
                $sptBelumDitugaskan = $db->table('spt')
                    ->where('id_opd', $idOpd)
                    ->where('status !=', 'Selesai')
                    ->where('status_penugasan', 'belum_ditugaskan')
                    ->countAllResults();

                $arsipMenungguVerifikasi = (clone $buildScopedArsip())
                    ->whereIn('status_verifikasi', ['Menunggu', 'Belum_Diproses', 'Belum Diproses'])
                    ->countAllResults();

                $baRevisiCount = $db->table('berita_acara')
                    ->where('id_opd', $idOpd)
                    ->where('status_persetujuan', 'Revisi')
                    ->countAllResults();

                $data['action_items_kabid'] = [
                    'spt_belum_ditugaskan' => $sptBelumDitugaskan,
                    'arsip_menunggu'       => $arsipMenungguVerifikasi,
                    'ba_menunggu'          => $stats['ba_menanti_kabid'] ?? 0,
                    'dokumen_revisi'       => $baRevisiCount,
                    'total_action'         => ($sptBelumDitugaskan + $arsipMenungguVerifikasi + ($stats['ba_menanti_kabid'] ?? 0) + $baRevisiCount),
                ];

                // 7. Monitoring Penugasan dan Proses Alih Media (SPT & Arsiparis)
                $data['workflow_kabid_list'] = $this->getWorkflowMonitoring($idOpd);
                break;

            case 'Arsiparis':
                $userId = $data['user_id'] ?? null;
                $idOpd  = $data['id_opd'] ?? null;

                // 1. PEKERJAAN BELUM DIKERJAKAN (PRIORITAS UTAMA)
                // Daftar SPT yang sudah diterbitkan & ditugaskan ke Arsiparis login, tetapi alih media belum dimulai
                $assignedSptList = [];
                $sptBelumDikerjakanList = [];

                if (!empty($userId)) {
                    $assignedSptList = $db->table('spt_assignments sa')
                        ->select('sa.id_assignment, sa.created_at as tanggal_penugasan, sa.status as status_assignment, s.id_spt, s.nomor_spt, s.tanggal_spt, s.perihal, s.status as status_spt, s.file_spt, s.tanggal_mulai, s.tanggal_selesai, (SELECT COUNT(a.id_arsip) FROM arsip a WHERE a.id_spt = s.id_spt) as jumlah_arsip')
                        ->join('spt s', 's.id_spt = sa.id_spt')
                        ->where('sa.id_user', $userId)
                        ->where('s.status !=', 'Selesai')
                        ->orderBy('sa.created_at', 'DESC')
                        ->get()
                        ->getResultArray();

                    foreach ($assignedSptList as $sptRow) {
                        $countArsip = (int)($sptRow['jumlah_arsip'] ?? 0);
                        if ($countArsip === 0) {
                            $sptRow['status_pekerjaan'] = 'Belum Dimulai';
                            $sptBelumDikerjakanList[] = $sptRow;
                        }
                    }
                }

                $data['spt_belum_dikerjakan'] = $sptBelumDikerjakanList;

                // 2. RINGKASAN PEKERJAAN (4 KARTU STATISTIK DINAMIS)
                // - Total Penugasan SPT
                // - Pekerjaan Belum Dimulai
                // - Arsip Sedang Diproses
                // - Arsip Menunggu Verifikasi
                $totalPenugasanSpt = !empty($userId) ? $db->table('spt_assignments')->where('id_user', $userId)->countAllResults() : 0;
                $pekerjaanBelumDimulaiCount = count($sptBelumDikerjakanList);

                $scopedArsipUser = function() use ($db, $userId) {
                    $builder = $db->table('arsip');
                    if (!empty($userId)) {
                        $builder->where('created_by', $userId);
                    } else {
                        $builder->where('1 = 0');
                    }
                    return $builder;
                };

                $totalArsipUser = !empty($userId) ? (clone $scopedArsipUser())->countAllResults() : 0;

                $arsipSedangDiproses = !empty($userId) ? (clone $scopedArsipUser())
                    ->whereIn('status_verifikasi', ['Proses', 'Dalam_Proses', 'Sedang Diproses', 'Sedang_Diproses', 'Draft'])
                    ->countAllResults() : 0;

                $arsipMenungguVerifikasi = !empty($userId) ? (clone $scopedArsipUser())
                    ->whereIn('status_verifikasi', ['Menunggu', 'Belum_Diproses', 'Belum Diproses'])
                    ->countAllResults() : 0;

                $arsipSelesai = !empty($userId) ? (clone $scopedArsipUser())
                    ->whereIn('status_verifikasi', ['Selesai', 'Disetujui', 'Terverifikasi'])
                    ->countAllResults() : 0;

                $arsipRevisi = !empty($userId) ? (clone $scopedArsipUser())
                    ->whereIn('status_verifikasi', ['Ditolak', 'Revisi', 'Gagal', 'Dibatalkan'])
                    ->countAllResults() : 0;

                $stats['penugasan_spt']             = $totalPenugasanSpt;
                $stats['pekerjaan_belum_dimulai']   = $pekerjaanBelumDimulaiCount;
                $stats['arsip_sedang_diproses']     = $arsipSedangDiproses;
                $stats['arsip_menunggu_verifikasi'] = $arsipMenungguVerifikasi;
                $stats['arsip_diunggah']            = $totalArsipUser; // kompatibilitas

                // 3. MONITORING PROSES ALIH MEDIA (ARSIP BERJALAN & TAHAPAN AKTUAL)
                $monitoringArsip = [];
                if (!empty($userId)) {
                    $arsipRows = $db->table('arsip a')
                        ->select('a.id_arsip, a.nomor_arsip, a.nama_arsip, a.status_verifikasi, a.created_at, a.updated_at, a.file_arsip, s.id_spt, s.nomor_spt')
                        ->join('spt s', 's.id_spt = a.id_spt', 'left')
                        ->where('a.created_by', $userId)
                        ->orderBy('COALESCE(a.updated_at, a.created_at) DESC', '', false)
                        ->limit(10)
                        ->get()
                        ->getResultArray();

                    foreach ($arsipRows as $aRow) {
                        $st = $aRow['status_verifikasi'] ?? 'Draft';
                        if (in_array($st, ['Selesai', 'Disetujui', 'Terverifikasi'])) {
                            $tahapan = 'Selesai Diverifikasi';
                            $statusClass = 'badge-success';
                        } elseif (in_array($st, ['Menunggu', 'Belum_Diproses'])) {
                            $tahapan = 'Menunggu Verifikasi Kabid';
                            $statusClass = 'badge-warning';
                        } elseif (in_array($st, ['Proses', 'Dalam_Proses'])) {
                            $tahapan = 'Alih Media & Pemindaian';
                            $statusClass = 'badge-info';
                        } elseif (in_array($st, ['Ditolak', 'Revisi', 'Gagal'])) {
                            $tahapan = 'Perlu Perbaikan / Revisi';
                            $statusClass = 'badge-danger';
                        } else {
                            $tahapan = 'Penyusunan Metadata Arsip';
                            $statusClass = 'badge-secondary';
                        }

                        $aRow['tahapan_label'] = $tahapan;
                        $aRow['status_class']  = $statusClass;
                        $monitoringArsip[]     = $aRow;
                    }
                }
                $data['monitoring_arsip_list'] = $monitoringArsip;

                // 4. STATUS VERIFIKASI ARSIP (SUMMARY STATUS + DONUT CHART)
                $selesaiPct = $totalArsipUser > 0 ? round(($arsipSelesai / $totalArsipUser) * 100, 1) : 0;
                $prosesPct  = $totalArsipUser > 0 ? round((($arsipSedangDiproses + $arsipMenungguVerifikasi) / $totalArsipUser) * 100, 1) : 0;
                $gagalPct   = $totalArsipUser > 0 ? round(($arsipRevisi / $totalArsipUser) * 100, 1) : 0;

                $data['status_arsip_arsiparis'] = [
                    'selesai'     => $arsipSelesai,
                    'proses'      => $arsipSedangDiproses + $arsipMenungguVerifikasi,
                    'telaah'      => $arsipMenungguVerifikasi,
                    'diproses'    => $arsipSedangDiproses,
                    'gagal'       => $arsipRevisi,
                    'total'       => $totalArsipUser,
                    'selesai_pct' => $selesaiPct,
                    'proses_pct'  => $prosesPct,
                    'gagal_pct'   => $gagalPct,
                ];

                // 5. BERITA ACARA ARSIPARIS
                $baQuery = $db->table('berita_acara ba')
                    ->select('ba.id_berita_acara, ba.nomor_ba, ba.tanggal_ba, ba.status_persetujuan, ba.catatan_revisi, s.id_spt, s.nomor_spt, u.nama as nama_pelaksana')
                    ->join('spt s', 's.id_spt = ba.id_spt', 'left')
                    ->join('users u', 'u.id_user = ba.id_pelaksana', 'left');

                if (!empty($userId)) {
                    $baQuery->where('(ba.id_pelaksana = ' . $db->escape($userId) . ' OR ba.created_by = ' . $db->escape($userId) . ')');
                } else {
                    $baQuery->where('1 = 0');
                }

                $baList = (clone $baQuery)->orderBy('ba.created_at', 'DESC')->limit(6)->get()->getResultArray();
                $stats['ba_menanti_verifikasi'] = (clone $baQuery)->whereIn('ba.status_persetujuan', ['Draf_Kabid', 'Menunggu_Pimpinan', 'Draft', 'Menunggu'])->countAllResults();
                $data['ba_arsiparis_list'] = $baList;

                // Tren Pengunggahan Arsip (Bar Chart) untuk visual pendukung
                $selectedYear = (string)date('Y');
                $monthlyRes = (clone $scopedArsipUser())
                    ->select('MONTH(created_at) as bln, COUNT(id_arsip) as total')
                    ->where('created_at IS NOT NULL')
                    ->where('YEAR(created_at)', $selectedYear)
                    ->groupBy('MONTH(created_at)')
                    ->get()
                    ->getResultArray();

                $months = array_fill(1, 12, 0);
                $peakCount = 0;
                $peakMonthNum = 0;
                $monthNamesIndo = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];

                foreach ($monthlyRes as $m) {
                    $bln = (int)$m['bln'];
                    $tot = (int)$m['total'];
                    if ($bln >= 1 && $bln <= 12) {
                        $months[$bln] = $tot;
                        if ($tot > $peakCount) {
                            $peakCount = $tot;
                            $peakMonthNum = $bln;
                        }
                    }
                }

                $peakMonthText = $peakCount > 0 
                    ? ($monthNamesIndo[$peakMonthNum] ?? 'September') . " ({$peakCount})"
                    : "-";

                $activeMonths = count(array_filter($months));
                $avgPerCycle = $activeMonths > 0 ? round(array_sum($months) / $activeMonths, 1) : 0;

                $data['monthly_arsip_arsiparis'] = [
                    'labels'        => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    'values'        => array_values($months),
                    'tahun'         => $selectedYear,
                    'peak_text'     => $peakMonthText,
                    'avg_per_cycle' => $avgPerCycle,
                ];

                $data['recent_assigned_spt'] = $data['spt_belum_dikerjakan'];
                break;
        }

        $data['stats'] = $stats;

        // Fallback jika nama_role kosong atau default ke Admin Pemkab
        if (empty($data['nama_role']) && empty($data['workflow_monitoring_list'])) {
            $data['pending_work_summary']     = $this->getPendingWorkSummary();
            $data['workflow_monitoring_list'] = $this->getWorkflowMonitoring();
            $data['opd_progress_list']        = $this->getOpdProgress();
        }

        // Ambil daftar seluruh OPD secara dinamis dari database untuk filter & grafik (sesuai role)
        $opdBuilder = $db->table('opd')
            ->select('id_opd, kode_opd, nama_opd')
            ->orderBy('nama_opd', 'ASC');

        if ($data['nama_role'] !== 'Admin_Pemkab' && !empty($data['id_opd'])) {
            $opdBuilder->where('id_opd', $data['id_opd']);
        }
        $allOpd = $opdBuilder->get()->getResultArray();
        $data['all_opd'] = $allOpd;

        // Ambil daftar periode (kurun waktu / tahun) yang tersedia pada sistem
        $periodsRaw = $db->table('arsip')
            ->select('kurun_waktu')
            ->where('kurun_waktu IS NOT NULL')
            ->where('kurun_waktu !=', '')
            ->distinct()
            ->get()
            ->getResultArray();

        $periods = array_filter(array_column($periodsRaw, 'kurun_waktu'));
        
        $yearsRaw = $db->table('arsip')
            ->select('YEAR(created_at) as yr')
            ->where('created_at IS NOT NULL')
            ->distinct()
            ->get()
            ->getResultArray();
        foreach ($yearsRaw as $y) {
            if (!empty($y['yr'])) {
                $periods[] = (string)$y['yr'];
            }
        }
        $periods[] = (string)date('Y');
        $periods[] = (string)(date('Y') - 1);
        $periods = array_values(array_unique(array_filter($periods)));
        rsort($periods);
        $data['all_periods'] = $periods;

        // Query data grafik awal (seluruh OPD diurutkan dari jumlah arsip terbanyak)
        $chartBuilder = $db->table('opd o')
            ->select('o.id_opd, o.nama_opd, o.kode_opd, COUNT(a.id_arsip) AS total_arsip')
            ->join('arsip a', 'a.id_opd = o.id_opd', 'left');

        if ($data['nama_role'] !== 'Admin_Pemkab' && !empty($data['id_opd'])) {
            $chartBuilder->where('o.id_opd', $data['id_opd']);
        }

        $chartQuery = $chartBuilder
            ->groupBy('o.id_opd, o.nama_opd, o.kode_opd')
            ->orderBy('total_arsip', 'DESC')
            ->orderBy('o.nama_opd', 'ASC')
            ->get()
            ->getResultArray();

        $data['chart_labels'] = array_column($chartQuery, 'nama_opd');
        $data['chart_values'] = array_map('intval', array_column($chartQuery, 'total_arsip'));

        // Data Aktivitas Terbaru dari Activity Log
        $activityLogModel = new ActivityLogModel();
        $rawActivities = $activityLogModel->getActivityLogsWithFilter()
            ->limit(10)
            ->get()
            ->getResultArray();

        $formattedActivities = [];
        foreach ($rawActivities as $act) {
            $module = strtolower($act['module'] ?? '');
            $action = strtolower($act['action'] ?? '');
            $desc   = trim($act['description'] ?? '');

            $icon = 'bi-file-earmark-text';
            $colorClass = 'icon-blue';

            // Tentukan icon, colorClass, dan badge berdasarkan modul dan action
            $actionBadge = 'Aktivitas';
            $badgeClass  = 'badge-status-blue';

            if (strpos($action, 'switch_role') !== false || strpos($action, 'switch role') !== false) {
                $icon = 'bi-arrow-left-right';
                $colorClass = 'icon-amber';
                $actionBadge = 'Switch Role';
                $badgeClass  = 'badge-status-amber';
            } elseif (strpos($action, 'switch_back') !== false || strpos($action, 'switch-back') !== false) {
                $icon = 'bi-arrow-return-left';
                $colorClass = 'icon-blue';
                $actionBadge = 'Switch Back';
                $badgeClass  = 'badge-status-blue';
            } elseif (strpos($module, 'user') !== false) {
                if (strpos($action, 'create') !== false || strpos($action, 'insert') !== false || strpos($action, 'tambah') !== false) {
                    $icon = 'bi-person-plus';
                    $colorClass = 'icon-green';
                    $actionBadge = 'Tambah Data';
                    $badgeClass  = 'badge-status-green';
                } elseif (strpos($action, 'delete') !== false || strpos($action, 'hapus') !== false) {
                    $icon = 'bi-person-dash';
                    $colorClass = 'icon-rose';
                    $actionBadge = 'Hapus';
                    $badgeClass  = 'badge-status-rose';
                } elseif (strpos($action, 'switch') !== false) {
                    $icon = 'bi-arrow-left-right';
                    $colorClass = 'icon-amber';
                    $actionBadge = 'Switch Role';
                    $badgeClass  = 'badge-status-amber';
                } else {
                    $icon = 'bi-person-gear';
                    $colorClass = 'icon-blue';
                    $actionBadge = 'Pembaruan';
                    $badgeClass  = 'badge-status-blue';
                }
            } elseif (strpos($module, 'penilaian') !== false) {
                if (strpos($action, 'revisi') !== false || strpos($desc, 'revisi') !== false) {
                    $icon = 'bi-arrow-repeat';
                    $colorClass = 'icon-amber';
                    $actionBadge = 'Revisi';
                    $badgeClass  = 'badge-status-amber';
                } else {
                    $icon = 'bi-check2-circle';
                    $colorClass = 'icon-purple';
                    $actionBadge = 'Penilaian';
                    $badgeClass  = 'badge-status-purple';
                }
            } elseif (strpos($module, 'arsip') !== false) {
                if (strpos($action, 'verify') !== false || strpos($action, 'verifikasi') !== false || strpos($desc, 'verifikasi') !== false) {
                    $icon = 'bi-shield-check';
                    $colorClass = 'icon-green';
                    $actionBadge = 'Verifikasi';
                    $badgeClass  = 'badge-status-green';
                } elseif (strpos($action, 'delete') !== false || strpos($action, 'hapus') !== false) {
                    $icon = 'bi-trash3';
                    $colorClass = 'icon-rose';
                    $actionBadge = 'Hapus';
                    $badgeClass  = 'badge-status-rose';
                } elseif (strpos($action, 'create') !== false || strpos($action, 'insert') !== false || strpos($action, 'unggah') !== false) {
                    $icon = 'bi-cloud-arrow-up';
                    $colorClass = 'icon-blue';
                    $actionBadge = 'Unggah Arsip';
                    $badgeClass  = 'badge-status-blue';
                } else {
                    $icon = 'bi-file-earmark-text';
                    $colorClass = 'icon-blue';
                    $actionBadge = 'Arsip';
                    $badgeClass  = 'badge-status-blue';
                }
            } elseif (strpos($module, 'spt') !== false) {
                if (strpos($action, 'assign') !== false || strpos($desc, 'menugaskan') !== false) {
                    $icon = 'bi-person-badge';
                    $colorClass = 'icon-amber';
                    $actionBadge = 'Penugasan';
                    $badgeClass  = 'badge-status-amber';
                } elseif (strpos($action, 'delete') !== false || strpos($action, 'hapus') !== false) {
                    $icon = 'bi-trash3';
                    $colorClass = 'icon-rose';
                    $actionBadge = 'Hapus';
                    $badgeClass  = 'badge-status-rose';
                } else {
                    $icon = 'bi-file-text';
                    $colorClass = 'icon-amber';
                    $actionBadge = 'SPT';
                    $badgeClass  = 'badge-status-amber';
                }
            } elseif (strpos($module, 'berita') !== false || strpos($module, 'acara') !== false) {
                $icon = 'bi-file-earmark-check';
                $colorClass = 'icon-rose';
                $actionBadge = 'Berita Acara';
                $badgeClass  = 'badge-status-rose';
            } elseif (strpos($module, 'opd') !== false) {
                $icon = 'bi-bank2';
                $colorClass = 'icon-green';
                $actionBadge = 'Master OPD';
                $badgeClass  = 'badge-status-green';
            } elseif (strpos($module, 'bidang') !== false) {
                $icon = 'bi-diagram-3';
                $colorClass = 'icon-green';
                $actionBadge = 'Master Bidang';
                $badgeClass  = 'badge-status-green';
            }

            // Prioritaskan deskripsi aktual dari database
            if (!empty($desc)) {
                $title = $desc;
            } elseif (!empty($act['action'])) {
                $title = ucfirst($act['action']) . ' data ' . ($act['module'] ?? 'Sistem');
            } else {
                $title = 'Aktivitas Sistem';
            }

            // Format nama user disertai OPD jika tersedia
            $rawUser = trim((string)($act['nama_user'] ?? ''));
            $rawOpd  = trim((string)($act['nama_opd'] ?? ''));

            if (!empty($rawUser)) {
                $userName = $rawUser;
                if (!empty($rawOpd) && stripos($userName, 'pemkab') === false && stripos($rawOpd, 'pemkab') === false) {
                    $userName .= ' (' . $rawOpd . ')';
                }
            } else {
                $userName = 'Administrator Pemkab';
            }

            $formattedActivities[] = [
                'title'        => $title,
                'user'         => $userName,
                'icon'         => $icon,
                'color_class'  => $colorClass,
                'badge'        => $actionBadge,
                'badge_class'  => $badgeClass,
                'date'         => date('d M Y', strtotime($act['created_at'])),
                'time'         => date('H:i', strtotime($act['created_at'])),
                'created_at'   => $act['created_at']
            ];
        }

        $data['recent_activities'] = $formattedActivities;

        return view('dashboard/index', $data);
    }

    /**
     * Endpoint AJAX untuk filter grafik distribusi arsip per OPD
     */
    public function chartData()
    {
        $idOpd   = $this->request->getGet('opd');
        $periode = $this->request->getGet('periode');
        $db      = \Config\Database::connect();

        $session    = session();
        $namaRole   = $session->get('nama_role');
        $sessionOpd = $session->get('id_opd');

        // Scoping jika role terikat ke OPD tertentu (bukan Admin_Pemkab)
        if ($namaRole !== 'Admin_Pemkab' && !empty($sessionOpd)) {
            $idOpd = $sessionOpd;
        }

        $builder = $db->table('opd o')
            ->select('o.id_opd, o.nama_opd, o.kode_opd, COUNT(a.id_arsip) AS total_arsip');

        if (!empty($periode) && $periode !== 'all') {
            $escapedPeriode = $db->escape($periode);
            $likePeriode    = $db->escape('%' . $periode . '%');
            $builder->join('arsip a', "a.id_opd = o.id_opd AND (a.kurun_waktu = {$escapedPeriode} OR a.kurun_waktu LIKE {$likePeriode} OR YEAR(a.created_at) = {$escapedPeriode})", 'left');
        } else {
            $builder->join('arsip a', 'a.id_opd = o.id_opd', 'left');
        }

        if (!empty($idOpd) && $idOpd !== 'all') {
            $builder->where('o.id_opd', $idOpd);
        }

        $result = $builder->groupBy('o.id_opd, o.nama_opd, o.kode_opd')
            ->orderBy('total_arsip', 'DESC')
            ->orderBy('o.nama_opd', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status'       => 'success',
            'labels'       => array_column($result, 'nama_opd'),
            'values'       => array_map('intval', array_column($result, 'total_arsip')),
            'count'        => count($result),
            'opd_progress' => $this->getOpdProgress($idOpd, $periode),
        ]);
    }

    /**
     * Endpoint AJAX untuk grafik arsip per bulan khusus Kepala Bidang (filter tahun, bidang, OPD)
     */
    public function kabidChartData()
    {
        $session  = session();
        $idOpd    = $session->get('id_opd');
        $idBidang = $session->get('id_bidang');
        $userId   = $session->get('user_id');
        $year     = $this->request->getGet('year') ?: '2026';
        $db       = \Config\Database::connect();

        if (!empty($userId) && (empty($idOpd) || empty($idBidang))) {
            $userModel = new UserModel();
            $userDb    = $userModel->find($userId);
            if ($userDb) {
                if (empty($idOpd) && !empty($userDb['id_opd'])) {
                    $idOpd = $userDb['id_opd'];
                }
                if (empty($idBidang) && !empty($userDb['id_bidang'])) {
                    $idBidang = $userDb['id_bidang'];
                }
            }
        }

        $builder = $db->table('arsip')
            ->select('MONTH(created_at) as bln, COUNT(id_arsip) as total')
            ->where('created_at IS NOT NULL')
            ->where('YEAR(created_at)', $year);

        if (!empty($idOpd)) {
            $builder->where('id_opd', $idOpd);
        }
        if (!empty($idBidang)) {
            $builder->where('id_bidang', $idBidang);
        }

        $res = $builder->groupBy('MONTH(created_at)')->get()->getResultArray();
        $months = array_fill(1, 12, 0);
        foreach ($res as $m) {
            $bln = (int)$m['bln'];
            if ($bln >= 1 && $bln <= 12) {
                $months[$bln] = (int)$m['total'];
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'year'   => (string)$year,
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'values' => array_values($months),
            'total'  => array_sum($months),
        ]);
    }

    /**
     * Mengambil data progres alih media per OPD (total, selesai, proses, menunggu, persentase)
     */
    private function getOpdProgress($idOpd = null, $periode = null): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('opd o')
            ->select("
                o.id_opd,
                o.nama_opd,
                o.kode_opd,
                COUNT(a.id_arsip) AS total_arsip,
                SUM(CASE WHEN a.status_verifikasi IN ('Selesai', 'Disetujui', 'Terverifikasi') THEN 1 ELSE 0 END) AS selesai_count,
                SUM(CASE WHEN a.status_verifikasi IN ('Draft', 'Proses', 'Dalam_Proses', 'Sedang Diproses') THEN 1 ELSE 0 END) AS proses_count,
                SUM(CASE WHEN a.status_verifikasi IN ('Menunggu', 'Belum_Diproses', 'Belum Diproses') THEN 1 ELSE 0 END) AS menunggu_count
            ");

        if (!empty($periode) && $periode !== 'all') {
            $escapedPeriode = $db->escape($periode);
            $likePeriode    = $db->escape('%' . $periode . '%');
            $builder->join('arsip a', "a.id_opd = o.id_opd AND (a.kurun_waktu = {$escapedPeriode} OR a.kurun_waktu LIKE {$likePeriode} OR YEAR(a.created_at) = {$escapedPeriode})", 'left');
        } else {
            $builder->join('arsip a', 'a.id_opd = o.id_opd', 'left');
        }

        if (!empty($idOpd) && $idOpd !== 'all') {
            $builder->where('o.id_opd', $idOpd);
        }

        $rows = $builder->groupBy('o.id_opd, o.nama_opd, o.kode_opd')
            ->orderBy('total_arsip', 'DESC')
            ->orderBy('o.nama_opd', 'ASC')
            ->get()
            ->getResultArray();

        $progressList = [];
        foreach ($rows as $r) {
            $tot = (int)$r['total_arsip'];
            $sel = (int)$r['selesai_count'];
            $pro = (int)$r['proses_count'];
            $men = (int)$r['menunggu_count'];
            $pct = $tot > 0 ? round(($sel / $tot) * 100, 1) : 0;

            $progressList[] = [
                'id_opd'         => $r['id_opd'],
                'nama_opd'       => $r['nama_opd'],
                'kode_opd'       => $r['kode_opd'] ?? '-',
                'total_arsip'    => $tot,
                'selesai_count'  => $sel,
                'proses_count'   => $pro,
                'menunggu_count' => $men,
                'persentase'     => $pct,
            ];
        }

        return $progressList;
    }

    /**
     * Mengambil daftar monitoring workflow alih media arsip se-kabupaten
     */
    private function getWorkflowMonitoring($idOpd = null): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('spt s')
            ->select('
                s.id_spt,
                s.nomor_spt,
                s.tanggal_spt,
                s.status as status_spt,
                s.status_penugasan,
                s.id_opd,
                s.id_pimpinan,
                s.created_at,
                s.updated_at,
                o.nama_opd,
                pimpinan.nama as nama_pimpinan
            ')
            ->join('opd o', 'o.id_opd = s.id_opd', 'left')
            ->join('users pimpinan', 'pimpinan.id_user = s.id_pimpinan', 'left');

        if (!empty($idOpd) && $idOpd !== 'all') {
            $builder->where('s.id_opd', $idOpd);
        }

        $spts = $builder->orderBy('s.tanggal_spt', 'DESC')
            ->orderBy('s.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $workflowList = [];

        foreach ($spts as $spt) {
            $idSpt = $spt['id_spt'];

            // 1. Ambil penugasan arsiparis
            $assignees = $db->table('spt_assignments sa')
                ->select('u.nama, sa.status')
                ->join('users u', 'u.id_user = sa.id_user', 'left')
                ->where('sa.id_spt', $idSpt)
                ->get()
                ->getResultArray();
            $assignedNames = array_filter(array_column($assignees, 'nama'));

            // 2. Hitung arsip terkait
            $arsipStats = $db->table('arsip')
                ->select("
                    COUNT(*) as total,
                    SUM(CASE WHEN status_verifikasi IN ('Selesai', 'Disetujui', 'Terverifikasi') THEN 1 ELSE 0 END) as selesai,
                    SUM(CASE WHEN status_verifikasi IN ('Menunggu', 'Belum_Diproses') THEN 1 ELSE 0 END) as menunggu,
                    SUM(CASE WHEN status_verifikasi IN ('Draft', 'Proses') THEN 1 ELSE 0 END) as draft
                ")
                ->where('id_spt', $idSpt)
                ->get()
                ->getRowArray();
            $totalArsip   = (int)($arsipStats['total'] ?? 0);
            $menungguArsip= (int)($arsipStats['menunggu'] ?? 0);

            // 3. Ambil Berita Acara terbaru terkait SPT
            $ba = $db->table('berita_acara ba')
                ->select('ba.*, u_pelaksana.nama as nama_pelaksana, u_kabid.nama as nama_kabid, u_pimpinan.nama as nama_pimpinan_ba')
                ->join('users u_pelaksana', 'u_pelaksana.id_user = ba.id_pelaksana', 'left')
                ->join('users u_kabid', 'u_kabid.id_user = ba.id_kabid', 'left')
                ->join('users u_pimpinan', 'u_pimpinan.id_user = ba.id_pimpinan', 'left')
                ->where('ba.id_spt', $idSpt)
                ->orderBy('ba.created_at', 'DESC')
                ->get()
                ->getRowArray();

            // 4. Ambil nama Kepala Bidang di OPD terkait dari database jika tersedia
            $kabidUser = $db->table('users u')
                ->select('u.nama')
                ->join('user_roles ur', 'ur.id_user = u.id_user', 'inner')
                ->join('roles r', 'r.id_role = ur.id_role', 'inner')
                ->where('u.id_opd', $spt['id_opd'])
                ->whereIn('r.nama_role', ['Kepala_Bidang', 'Kepala Bidang', 'Kabid'])
                ->get()
                ->getRowArray();
            $namaKabidOpd = !empty($kabidUser['nama']) ? trim($kabidUser['nama']) : null;

            // Tentukan Tahapan, Petugas, Status berdasarkan alur SIAMA
            $tahapNum     = 1;
            $tahapLabel   = 'Penerbitan SPT';
            $tahapFull    = 'Tahap 1: Penerbitan SPT oleh Pimpinan';
            $petugasRaw   = $namaKabidOpd;
            $petugasRole  = 'Kepala Bidang';
            $statusProses = 'Menunggu Penugasan Arsiparis';
            $statusClass  = 'badge-status-amber';
            $lastUpdated  = $spt['updated_at'] ?? $spt['created_at'];

            if (!empty($ba)) {
                if (!empty($ba['updated_at'])) {
                    $lastUpdated = $ba['updated_at'];
                } elseif (!empty($ba['created_at'])) {
                    $lastUpdated = $ba['created_at'];
                }
            }

            if ($spt['status_spt'] === 'Selesai' || (!empty($ba) && $ba['status_persetujuan'] === 'Selesai')) {
                $tahapNum     = 5;
                $tahapLabel   = 'Selesai';
                $tahapFull    = 'Tahap 5: Persetujuan Pimpinan (Selesai)';
                $petugasRaw   = !empty($ba['nama_pimpinan_ba']) ? $ba['nama_pimpinan_ba'] : (!empty($spt['nama_pimpinan']) ? $spt['nama_pimpinan'] : null);
                $petugasRole  = 'Pimpinan';
                $statusProses = 'Selesai';
                $statusClass  = 'badge-status-green';
            } elseif (!empty($ba) && in_array($ba['status_persetujuan'], ['Draf_Pimpinan', 'Menunggu_Pimpinan'])) {
                $tahapNum     = 5;
                $tahapLabel   = 'Persetujuan Pimpinan';
                $tahapFull    = 'Tahap 5: Persetujuan Pimpinan';
                $petugasRaw   = !empty($ba['nama_pimpinan_ba']) ? $ba['nama_pimpinan_ba'] : (!empty($spt['nama_pimpinan']) ? $spt['nama_pimpinan'] : null);
                $petugasRole  = 'Pimpinan';
                $statusProses = 'Menunggu TTD Pimpinan';
                $statusClass  = 'badge-status-purple';
            } elseif (!empty($ba) && $ba['status_persetujuan'] === 'Draf_Kabid') {
                $tahapNum     = 4;
                $tahapLabel   = 'Verifikasi Kabid';
                $tahapFull    = 'Tahap 4: Verifikasi oleh Kepala Bidang';
                $petugasRaw   = !empty($ba['nama_kabid']) ? $ba['nama_kabid'] : (!empty($namaKabidOpd) ? $namaKabidOpd : null);
                $petugasRole  = 'Kepala Bidang';
                $statusProses = 'Menunggu Verifikasi Kabid';
                $statusClass  = 'badge-status-amber';
            } elseif (!empty($ba) && $ba['status_persetujuan'] === 'Revisi') {
                $tahapNum     = 3;
                $tahapLabel   = 'Proses Alih Media';
                $tahapFull    = 'Tahap 3: Proses Alih Media oleh Arsiparis';
                $petugasRaw   = !empty($ba['nama_pelaksana']) ? $ba['nama_pelaksana'] : (!empty($assignedNames) ? implode(', ', $assignedNames) : null);
                $petugasRole  = 'Arsiparis';
                $statusProses = 'Perbaikan Draf (Revisi)';
                $statusClass  = 'badge-status-rose';
            } elseif ($spt['status_penugasan'] === 'ditugaskan') {
                if ($totalArsip === 0) {
                    $tahapNum     = 2;
                    $tahapLabel   = 'Penugasan Arsiparis';
                    $tahapFull    = 'Tahap 2: Penugasan Arsiparis oleh Kepala Bidang';
                    $petugasRaw   = !empty($assignedNames) ? implode(', ', $assignedNames) : null;
                    $petugasRole  = 'Arsiparis';
                    $statusProses = 'Menunggu Mulai Alih Media';
                    $statusClass  = 'badge-status-blue';
                } elseif ($menungguArsip > 0) {
                    $tahapNum     = 4;
                    $tahapLabel   = 'Verifikasi Kabid';
                    $tahapFull    = 'Tahap 4: Verifikasi oleh Kepala Bidang';
                    $petugasRaw   = !empty($namaKabidOpd) ? $namaKabidOpd : null;
                    $petugasRole  = 'Kepala Bidang';
                    $statusProses = 'Menunggu Verifikasi Arsip';
                    $statusClass  = 'badge-status-amber';
                } else {
                    $tahapNum     = 3;
                    $tahapLabel   = 'Proses Alih Media';
                    $tahapFull    = 'Tahap 3: Proses Alih Media oleh Arsiparis';
                    $petugasRaw   = !empty($assignedNames) ? implode(', ', $assignedNames) : null;
                    $petugasRole  = 'Arsiparis';
                    $statusProses = 'Sedang Alih Media';
                    $statusClass  = 'badge-status-blue';
                }
            } else {
                // Belum ditugaskan
                $tahapNum     = 1;
                $tahapLabel   = 'Penerbitan SPT';
                $tahapFull    = 'Tahap 1: Penerbitan SPT oleh Pimpinan';
                $petugasRaw   = !empty($namaKabidOpd) ? $namaKabidOpd : null;
                $petugasRole  = 'Kepala Bidang';
                $statusProses = 'Menunggu Penugasan Arsiparis';
                $statusClass  = 'badge-status-amber';
            }

            // Bersihkan suffix role dalam kurung jika ada (misal: "Kepala Dinas (Pimpinan)" -> "Kepala Dinas", "Budi Santoso (Arsiparis)" -> "Budi Santoso")
            $cleanPetugas = !empty($petugasRaw) ? trim(preg_replace('/\s*\((Arsiparis|Pimpinan|Kepala Bidang|Kabid)\)/i', '', $petugasRaw)) : '';

            // Tentukan apakah ada nama spesifik pengguna yang berbeda dari nama role
            $hasSpecificUser = false;
            $displayName = null;

            if (!empty($cleanPetugas)) {
                $isRoleNameOnly = (strcasecmp($cleanPetugas, $petugasRole) === 0)
                    || (strcasecmp($cleanPetugas, 'Kepala Bidang') === 0 && in_array($petugasRole, ['Kepala Bidang', 'Kabid']))
                    || (strcasecmp($cleanPetugas, 'Arsiparis') === 0 && $petugasRole === 'Arsiparis')
                    || (strcasecmp($cleanPetugas, 'Pimpinan') === 0 && $petugasRole === 'Pimpinan');

                if (!$isRoleNameOnly) {
                    $hasSpecificUser = true;
                    $displayName = $cleanPetugas;
                }
            }

            $finalPetugas = $hasSpecificUser ? $displayName : $petugasRole;

            // Penentuan kondisi proses khusus untuk monitoring penugasan Kabid & alur SIAMA
            $kondisiProses = 'menunggu_penugasan';
            if ($spt['status_spt'] === 'Selesai' || $statusProses === 'Selesai') {
                $kondisiProses = 'selesai';
            } elseif (in_array($statusProses, ['Menunggu Verifikasi Kabid', 'Menunggu Verifikasi Arsip'])) {
                $kondisiProses = 'menunggu_verifikasi';
            } elseif (in_array($statusProses, ['Sedang Alih Media', 'Perbaikan Draf (Revisi)'])) {
                $kondisiProses = 'sedang_diproses';
            } elseif ($spt['status_penugasan'] === 'ditugaskan' && $totalArsip === 0) {
                $kondisiProses = 'sudah_ditugaskan';
            } elseif ($spt['status_penugasan'] === 'belum_ditugaskan' || empty($assignedNames)) {
                $kondisiProses = 'menunggu_penugasan';
            }

            $workflowList[] = [
                'id_spt'             => $spt['id_spt'],
                'nomor_spt'          => $spt['nomor_spt'],
                'id_opd'             => $spt['id_opd'],
                'nama_opd'           => $spt['nama_opd'] ?? '-',
                'tanggal_spt'        => $spt['tanggal_spt'],
                'total_arsip'        => $totalArsip,
                'tahap_num'          => $tahapNum,
                'tahap_label'        => $tahapLabel,
                'tahap_full'         => $tahapFull,
                'petugas'            => $finalPetugas,
                'petugas_name'       => $displayName,
                'petugas_role'       => $petugasRole,
                'has_specific_user'  => $hasSpecificUser,
                'status_proses'      => $statusProses,
                'status_class'       => $statusClass,
                'status_penugasan'   => $spt['status_penugasan'] ?? 'belum_ditugaskan',
                'kondisi_proses'     => $kondisiProses,
                'arsiparis_assigned' => !empty($assignedNames) ? implode(', ', $assignedNames) : null,
                'total_assigned'     => count($assignedNames),
                'last_updated'       => !empty($lastUpdated) ? date('d M Y, H:i', strtotime($lastUpdated)) : '-',
                'raw_updated_at'     => $lastUpdated,
                'id_berita_acara'    => $ba['id_berita_acara'] ?? null,
            ];
        }

        // Ambil juga Berita Acara mandiri (tanpa id_spt) agar pemantauan alur mencakup seluruh dokumen workflow
        $baBuilder = $db->table('berita_acara ba')
            ->select('
                ba.id_berita_acara,
                ba.nomor_ba,
                ba.tanggal_ba,
                ba.status_persetujuan,
                ba.id_opd,
                ba.created_at,
                ba.updated_at,
                o.nama_opd,
                u_pelaksana.nama as nama_pelaksana,
                u_kabid.nama as nama_kabid,
                u_pimpinan.nama as nama_pimpinan_ba
            ')
            ->join('opd o', 'o.id_opd = ba.id_opd', 'left')
            ->join('users u_pelaksana', 'u_pelaksana.id_user = ba.id_pelaksana', 'left')
            ->join('users u_kabid', 'u_kabid.id_user = ba.id_kabid', 'left')
            ->join('users u_pimpinan', 'u_pimpinan.id_user = ba.id_pimpinan', 'left')
            ->groupStart()
                ->where('ba.id_spt', null)
                ->orWhere('ba.id_spt', '')
            ->groupEnd();

        if (!empty($idOpd) && $idOpd !== 'all') {
            $baBuilder->where('ba.id_opd', $idOpd);
        }

        $standaloneBas = $baBuilder->orderBy('ba.tanggal_ba', 'DESC')
            ->orderBy('ba.created_at', 'DESC')
            ->get()
            ->getResultArray();

        foreach ($standaloneBas as $sba) {
            $arsipCount = $db->table('berita_acara_detail')
                ->where('id_berita_acara', $sba['id_berita_acara'])
                ->countAllResults();

            $tahapNum     = 4;
            $tahapLabel   = 'Verifikasi Kabid';
            $tahapFull    = 'Tahap 4: Verifikasi oleh Kepala Bidang';
            $petugasRaw   = $sba['nama_kabid'];
            $petugasRole  = 'Kepala Bidang';
            $statusProses = 'Menunggu Verifikasi Kabid';
            $statusClass  = 'badge-status-amber';
            $kondisiProses= 'menunggu_verifikasi';

            if ($sba['status_persetujuan'] === 'Selesai') {
                $tahapNum     = 5;
                $tahapLabel   = 'Selesai';
                $tahapFull    = 'Tahap 5: Persetujuan Pimpinan (Selesai)';
                $petugasRaw   = $sba['nama_pimpinan_ba'];
                $petugasRole  = 'Pimpinan';
                $statusProses = 'Selesai';
                $statusClass  = 'badge-status-green';
                $kondisiProses= 'selesai';
            } elseif (in_array($sba['status_persetujuan'], ['Draf_Pimpinan', 'Menunggu_Pimpinan'])) {
                $tahapNum     = 5;
                $tahapLabel   = 'Persetujuan Pimpinan';
                $tahapFull    = 'Tahap 5: Persetujuan Pimpinan';
                $petugasRaw   = $sba['nama_pimpinan_ba'];
                $petugasRole  = 'Pimpinan';
                $statusProses = 'Menunggu TTD Pimpinan';
                $statusClass  = 'badge-status-purple';
                $kondisiProses= 'menunggu_verifikasi';
            } elseif ($sba['status_persetujuan'] === 'Revisi') {
                $tahapNum     = 3;
                $tahapLabel   = 'Proses Alih Media';
                $tahapFull    = 'Tahap 3: Proses Alih Media oleh Arsiparis';
                $petugasRaw   = $sba['nama_pelaksana'];
                $petugasRole  = 'Arsiparis';
                $statusProses = 'Perbaikan Draf (Revisi)';
                $statusClass  = 'badge-status-rose';
                $kondisiProses= 'sedang_diproses';
            }

            $cleanPetugas = !empty($petugasRaw) ? trim(preg_replace('/\s*\((Arsiparis|Pimpinan|Kepala Bidang|Kabid)\)/i', '', $petugasRaw)) : '';
            $hasSpecificUser = !empty($cleanPetugas);
            $finalPetugas = $hasSpecificUser ? $cleanPetugas : $petugasRole;

            $workflowList[] = [
                'id_spt'             => 'ba-' . $sba['id_berita_acara'],
                'nomor_spt'          => 'BA: ' . $sba['nomor_ba'],
                'id_opd'             => $sba['id_opd'],
                'nama_opd'           => $sba['nama_opd'] ?? '-',
                'tanggal_spt'        => $sba['tanggal_ba'],
                'total_arsip'        => $arsipCount,
                'tahap_num'          => $tahapNum,
                'tahap_label'        => $tahapLabel,
                'tahap_full'         => $tahapFull,
                'petugas'            => $finalPetugas,
                'petugas_name'       => $cleanPetugas ?: null,
                'petugas_role'       => $petugasRole,
                'has_specific_user'  => $hasSpecificUser,
                'status_proses'      => $statusProses,
                'status_class'       => $statusClass,
                'status_penugasan'   => 'selesai',
                'kondisi_proses'     => $kondisiProses,
                'arsiparis_assigned' => $sba['nama_pelaksana'],
                'total_assigned'     => !empty($sba['nama_pelaksana']) ? 1 : 0,
                'last_updated'       => !empty($sba['updated_at']) ? date('d M Y, H:i', strtotime($sba['updated_at'])) : (!empty($sba['created_at']) ? date('d M Y, H:i', strtotime($sba['created_at'])) : '-'),
                'raw_updated_at'     => $sba['updated_at'] ?? $sba['created_at'],
                'id_berita_acara'    => $sba['id_berita_acara'],
            ];
        }

        return $workflowList;
    }

    /**
     * Mengambil ringkasan pekerjaan yang belum selesai / membutuhkan tindak lanjut per OPD
     * Dihitung secara dinamis dari database tanpa duplikasi data (Anti Double Counting).
     */
    private function getPendingWorkSummary(): array
    {
        $db = \Config\Database::connect();
        $allOpds = $db->table('opd')->orderBy('nama_opd', 'ASC')->get()->getResultArray();
        $pendingSummary = [];

        foreach ($allOpds as $opd) {
            $idOpd = $opd['id_opd'];

            // 1. Ambil seluruh SPT milik OPD ini
            $spts = $db->table('spt')
                ->where('id_opd', $idOpd)
                ->get()
                ->getResultArray();

            $activeSptCount = 0;
            $opdJobs = []; // Daftar pekerjaan alur workflow aktif di OPD ini

            foreach ($spts as $spt) {
                $idSpt = $spt['id_spt'];

                // Ambil Berita Acara terbaru terkait SPT ini jika ada
                $ba = $db->table('berita_acara')
                    ->where('id_spt', $idSpt)
                    ->orderBy('created_at', 'DESC')
                    ->get()
                    ->getRowArray();

                // Cek apakah pekerjaan SPT ini sudah selesai
                $isSelesai = ($spt['status'] === 'Selesai') 
                    || (!empty($ba) && $ba['status_persetujuan'] === 'Selesai')
                    || ($spt['status_penugasan'] === 'selesai' && !empty($ba) && $ba['status_persetujuan'] === 'Selesai');

                if ($isSelesai) {
                    // Dokumen yang sudah selesai TIDAK boleh dihitung sebagai pekerjaan tertunda
                    continue;
                }

                // SPT berstatus aktif dan belum selesai
                $activeSptCount++;

                // Ambil statistik arsip terkait SPT ini
                $arsipStats = $db->table('arsip')
                    ->select("
                        COUNT(*) as total,
                        SUM(CASE WHEN status_verifikasi IN ('Menunggu', 'Belum_Diproses', 'Belum Diproses') THEN 1 ELSE 0 END) as menunggu,
                        SUM(CASE WHEN status_verifikasi IN ('Draft', 'Proses') THEN 1 ELSE 0 END) as draft
                    ")
                    ->where('id_spt', $idSpt)
                    ->get()
                    ->getRowArray();

                $totalArsipInSpt   = (int)($arsipStats['total'] ?? 0);
                $menungguArisInSpt = (int)($arsipStats['menunggu'] ?? 0);

                // Pemetaan tahapan aktual berdasarkan alur proses SIAMA
                if (!empty($ba) && in_array($ba['status_persetujuan'], ['Draf_Pimpinan', 'Menunggu_Pimpinan'])) {
                    $tahapKey   = 'persetujuan_pimpinan';
                    $tahapLabel = 'Persetujuan Pimpinan';
                    $tahapBadge = 'badge-status-purple';
                } elseif (!empty($ba) && $ba['status_persetujuan'] === 'Draf_Kabid') {
                    $tahapKey   = 'verifikasi_kabid';
                    $tahapLabel = 'Verifikasi Kepala Bidang';
                    $tahapBadge = 'badge-status-amber';
                } elseif (!empty($ba) && $ba['status_persetujuan'] === 'Revisi') {
                    $tahapKey   = 'perbaikan_revisi';
                    $tahapLabel = 'Perbaikan Revisi';
                    $tahapBadge = 'badge-status-rose';
                } elseif ($spt['status_penugasan'] === 'ditugaskan') {
                    if ($totalArsipInSpt === 0) {
                        $tahapKey   = 'penugasan_arsiparis';
                        $tahapLabel = 'Penugasan Arsiparis';
                        $tahapBadge = 'badge-status-blue';
                    } elseif ($menungguArisInSpt > 0) {
                        $tahapKey   = 'verifikasi_kabid';
                        $tahapLabel = 'Verifikasi Kepala Bidang';
                        $tahapBadge = 'badge-status-amber';
                    } else {
                        $tahapKey   = 'proses_alih_media';
                        $tahapLabel = 'Proses Alih Media';
                        $tahapBadge = 'badge-status-blue';
                    }
                } else {
                    // Belum ditugaskan oleh Kepala Bidang
                    $tahapKey   = 'penugasan_arsiparis';
                    $tahapLabel = 'Penugasan Arsiparis';
                    $tahapBadge = 'badge-status-amber';
                }

                $opdJobs[] = [
                    'tahap_key'   => $tahapKey,
                    'tahap_label' => $tahapLabel,
                    'tahap_badge' => $tahapBadge,
                ];
            }

            // 2. Cek Berita Acara mandiri (tanpa id_spt) yang belum selesai di OPD ini
            $standaloneBas = $db->table('berita_acara')
                ->where('id_opd', $idOpd)
                ->groupStart()
                    ->where('id_spt', null)
                    ->orWhere('id_spt', '')
                ->groupEnd()
                ->whereIn('status_persetujuan', ['Draf_Kabid', 'Draf_Pimpinan', 'Revisi', 'Menunggu_Pimpinan'])
                ->get()
                ->getResultArray();

            foreach ($standaloneBas as $sba) {
                if (in_array($sba['status_persetujuan'], ['Draf_Pimpinan', 'Menunggu_Pimpinan'])) {
                    $tahapKey   = 'persetujuan_pimpinan';
                    $tahapLabel = 'Persetujuan Pimpinan';
                    $tahapBadge = 'badge-status-purple';
                } elseif ($sba['status_persetujuan'] === 'Draf_Kabid') {
                    $tahapKey   = 'verifikasi_kabid';
                    $tahapLabel = 'Verifikasi Kepala Bidang';
                    $tahapBadge = 'badge-status-amber';
                } else {
                    $tahapKey   = 'perbaikan_revisi';
                    $tahapLabel = 'Perbaikan Revisi';
                    $tahapBadge = 'badge-status-rose';
                }

                $opdJobs[] = [
                    'tahap_key'   => $tahapKey,
                    'tahap_label' => $tahapLabel,
                    'tahap_badge' => $tahapBadge,
                ];
            }

            // 3. Dokumen atau arsip yang benar-benar menunggu verifikasi di OPD ini
            $menungguArsipCount = (int)$db->table('arsip')
                ->where('id_opd', $idOpd)
                ->whereIn('status_verifikasi', ['Menunggu', 'Belum_Diproses', 'Belum Diproses'])
                ->countAllResults();

            $menungguBaCount = (int)$db->table('berita_acara')
                ->where('id_opd', $idOpd)
                ->whereIn('status_persetujuan', ['Draf_Kabid', 'Draf_Pimpinan', 'Menunggu_Pimpinan'])
                ->countAllResults();

            $menungguVerifikasiCount = $menungguArsipCount;
            $menungguVerifikasiLabel = 'Arsip';
            if ($menungguArsipCount === 0 && $menungguBaCount > 0) {
                $menungguVerifikasiCount = $menungguBaCount;
                $menungguVerifikasiLabel = 'BA';
            } elseif ($menungguArsipCount > 0 && $menungguBaCount > 0) {
                $menungguVerifikasiCount = $menungguArsipCount + $menungguBaCount;
                $menungguVerifikasiLabel = 'Dokumen';
            }

            // Total pekerjaan belum selesai adalah total pekerjaan aktif yang sedang berjalan di OPD ini
            $totalPending = count($opdJobs);

            // Jika ada arsip yang menunggu verifikasi mandiri namun belum tercakup dalam opdJobs
            if ($totalPending === 0 && $menungguArsipCount > 0) {
                $opdJobs[] = [
                    'tahap_key'   => 'verifikasi_kabid',
                    'tahap_label' => 'Verifikasi Kepala Bidang',
                    'tahap_badge' => 'badge-status-amber',
                ];
                $totalPending = count($opdJobs);
            }

            // Hanya masukkan OPD yang memiliki pekerjaan aktif
            if ($totalPending > 0) {
                $tahapanGrouped = [];
                foreach ($opdJobs as $job) {
                    $key = $job['tahap_key'];
                    if (!isset($tahapanGrouped[$key])) {
                        $tahapanGrouped[$key] = [
                            'label' => $job['tahap_label'],
                            'count' => 0,
                            'badge' => $job['tahap_badge'],
                        ];
                    }
                    $tahapanGrouped[$key]['count']++;
                }

                $pendingSummary[] = [
                    'id_opd'                    => $idOpd,
                    'nama_opd'                  => $opd['nama_opd'],
                    'kode_opd'                  => $opd['kode_opd'] ?? '-',
                    'active_spt'                => $activeSptCount,
                    'menunggu_verifikasi'       => $menungguVerifikasiCount,
                    'menunggu_verifikasi_label' => $menungguVerifikasiLabel,
                    'total_pending'             => $totalPending,
                    'tahapan_list'              => array_values($tahapanGrouped),
                ];
            }
        }

        return $pendingSummary;
    }
}

