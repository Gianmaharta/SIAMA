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

        // Logika penarikan data sesuai nama_role
        switch ($data['nama_role']) {
            case 'Admin_Pemkab':
                $stats['total_opd']          = $db->table('opd')->countAllResults();
                $stats['total_users']        = $userModel->countAllResults();
                $stats['total_arsip']        = $arsipModel->countAllGlobal();
                $stats['total_spt']          = $sptModel->countAllGlobal();
                $stats['total_berita_acara'] = $beritaAcaraModel->countAllGlobal();
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

                // 2. Berita Acara Menanti TTD Pimpinan di OPD
                $baQueryCount = $db->table('berita_acara')
                    ->whereIn('status_persetujuan', ['Draf_Pimpinan', 'Menunggu_Pimpinan']);
                if (!empty($idOpd)) {
                    $baQueryCount->where('id_opd', $idOpd);
                } elseif (!empty($userId)) {
                    $baQueryCount->where('id_pimpinan', $userId);
                }
                $stats['ba_menanti_pimpinan'] = $baQueryCount->countAllResults();

                // 3. SPT Terbaru untuk role Pimpinan (limit 4)
                $recentSptQuery = $db->table('spt')
                    ->select('nomor_spt, tanggal_spt, perihal');
                if (!empty($idOpd)) {
                    $recentSptQuery->where('id_opd', $idOpd);
                } elseif (!empty($userId)) {
                    $recentSptQuery->where('id_pimpinan', $userId);
                }
                $data['recent_spt'] = $recentSptQuery
                    ->orderBy('tanggal_spt', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->limit(4)
                    ->get()
                    ->getResultArray();

                // 4. Berita Acara Menanti TTD untuk role Pimpinan (limit 4)
                $recentBaQuery = $db->table('berita_acara ba')
                    ->select('ba.nomor_ba, ba.tanggal_ba, ba.status_persetujuan, s.perihal')
                    ->join('spt s', 's.id_spt = ba.id_spt', 'left');
                if (!empty($idOpd)) {
                    $recentBaQuery->where('ba.id_opd', $idOpd);
                } elseif (!empty($userId)) {
                    $recentBaQuery->where('ba.id_pimpinan', $userId);
                }
                $data['recent_ba'] = $recentBaQuery
                    ->orderBy('ba.created_at', 'DESC')
                    ->limit(4)
                    ->get()
                    ->getResultArray();
                break;

            case 'Admin_OPD':
            case 'Administrator OPD':
                $idOpd = $data['id_opd'] ?? null;
                if (!empty($idOpd)) {
                    $stats['total_users_opd']  = $userModel->where('id_opd', $idOpd)->countAllResults();
                    $stats['total_bidang_opd'] = $db->table('bidang')->where('id_opd', $idOpd)->countAllResults();
                    $stats['total_arsip_opd']  = $arsipModel->countByOpd($idOpd);

                    // Ambil daftar pengguna di OPD
                    $data['users_opd'] = $db->table('users u')
                        ->select('u.id_user, u.nama, u.email, u.is_active, r.nama_role')
                        ->join('user_roles ur', 'ur.id_user = u.id_user', 'left')
                        ->join('roles r', 'r.id_role = ur.id_role', 'left')
                        ->where('u.id_opd', $idOpd)
                        ->orderBy('u.created_at', 'DESC')
                        ->limit(5)
                        ->get()
                        ->getResultArray();

                    // Ambil daftar bidang di OPD beserta jumlah arsip
                    $data['bidang_opd'] = $db->table('bidang b')
                        ->select('b.id_bidang, b.nama_bidang, COUNT(a.id_arsip) AS total_arsip')
                        ->join('arsip a', 'a.id_bidang = b.id_bidang', 'left')
                        ->where('b.id_opd', $idOpd)
                        ->groupBy('b.id_bidang, b.nama_bidang')
                        ->orderBy('b.nama_bidang', 'ASC')
                        ->limit(5)
                        ->get()
                        ->getResultArray();

                    // Ambil arsip terbaru di OPD
                    $data['recent_arsip_opd'] = $db->table('arsip a')
                        ->select('a.id_arsip, a.nomor_arsip, a.nama_arsip, a.created_at, a.status_verifikasi')
                        ->where('a.id_opd', $idOpd)
                        ->orderBy('a.created_at', 'DESC')
                        ->limit(5)
                        ->get()
                        ->getResultArray();
                } else {
                    $stats['total_users_opd']  = 0;
                    $stats['total_bidang_opd'] = 0;
                    $stats['total_arsip_opd']  = 0;
                    $data['users_opd']         = [];
                    $data['bidang_opd']        = [];
                    $data['recent_arsip_opd']  = [];
                }
                break;

            case 'Kepala_Bidang':
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

                // 2. Berita Acara Menanti Verifikasi Kabid
                $baCount = 0;
                if ($userId) {
                    $baCount = $beritaAcaraModel->countMenungguVerifikasiKabid($userId);
                }
                if ($baCount === 0 && !empty($idOpd)) {
                    $baCount = $db->table('berita_acara')
                        ->where('status_persetujuan', 'Draf_Kabid')
                        ->where('id_opd', $idOpd)
                        ->countAllResults();
                }
                $stats['ba_menanti_kabid'] = $baCount;

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

                // 5. Berita Acara Menanti Verifikasi Kabid
                $baQuery = $db->table('berita_acara ba')
                    ->select('ba.id_berita_acara, ba.nomor_ba, ba.tanggal_ba, ba.status_persetujuan, s.nomor_spt, u.nama as nama_pelaksana')
                    ->join('spt s', 's.id_spt = ba.id_spt', 'left')
                    ->join('users u', 'u.id_user = ba.id_pelaksana', 'left')
                    ->where('ba.status_persetujuan', 'Draf_Kabid');

                if ($userId) {
                    $escapedUser = $db->escape($userId);
                    $opdClause = !empty($idOpd) ? ' OR (ba.id_kabid IS NULL AND ba.id_opd = ' . $db->escape($idOpd) . ')' : '';
                    $baQuery->where("(ba.id_kabid = {$escapedUser}{$opdClause})");
                } elseif (!empty($idOpd)) {
                    $baQuery->where('ba.id_opd', $idOpd);
                }
                $data['ba_menanti_list'] = $baQuery->orderBy('ba.created_at', 'DESC')->limit(5)->get()->getResultArray();
                break;

            case 'Arsiparis':
                $userId = $data['user_id'] ?? null;

                // 1. Summary Cards
                $totalArsipUser = $userId ? $arsipModel->where('created_by', $userId)->countAllResults() : 0;
                $totalSptUser   = $userId ? $sptModel->countByPelaksana($userId) : 0;

                // Berita Acara yang dibuat/dikerjakan oleh Arsiparis
                $baQuery = $db->table('berita_acara ba')
                    ->select('ba.id_berita_acara, ba.nomor_ba, ba.tanggal_ba, ba.status_persetujuan, s.nomor_spt, u.nama as nama_pelaksana')
                    ->join('spt s', 's.id_spt = ba.id_spt', 'left')
                    ->join('users u', 'u.id_user = ba.id_pelaksana', 'left')
                    ->where('ba.id_pelaksana', $userId);

                $baMenantiList = (clone $baQuery)
                    ->whereIn('ba.status_persetujuan', ['Draf_Kabid', 'Menunggu_Pimpinan', 'Draft_Kabid', 'Draft', 'Menunggu'])
                    ->orderBy('ba.created_at', 'DESC')
                    ->limit(5)
                    ->get()
                    ->getResultArray();

                $totalBaMenanti = count($baMenantiList);

                // Jika tidak ada yang menanti verifikasi, ambil daftar Berita Acara terbaru Arsiparis
                if (empty($baMenantiList)) {
                    $baMenantiList = (clone $baQuery)
                        ->orderBy('ba.created_at', 'DESC')
                        ->limit(5)
                        ->get()
                        ->getResultArray();
                }

                $stats['arsip_diunggah']        = $totalArsipUser;
                $stats['penugasan_spt']         = $totalSptUser;
                $stats['ba_menanti_verifikasi'] = $totalBaMenanti;
                $data['ba_arsiparis_list']       = $baMenantiList;

                // 2. Status Verifikasi Arsip (Donut Chart) dari Database
                $scopedArsipUser = function() use ($db, $userId) {
                    return $db->table('arsip')->where('created_by', $userId);
                };

                $selesaiCount = (clone $scopedArsipUser())
                    ->whereIn('status_verifikasi', ['Selesai', 'Disetujui', 'Terverifikasi'])
                    ->countAllResults();

                $prosesCount = (clone $scopedArsipUser())
                    ->whereIn('status_verifikasi', ['Proses', 'Dalam_Proses', 'Sedang Diproses', 'Sedang_Diproses', 'Menunggu', 'Draft'])
                    ->countAllResults();

                $gagalCount = (clone $scopedArsipUser())
                    ->whereIn('status_verifikasi', ['Ditolak', 'Gagal', 'Dibatalkan', 'Revisi'])
                    ->countAllResults();

                $sumKnown = $selesaiCount + $prosesCount + $gagalCount;
                if ($totalArsipUser > $sumKnown) {
                    $prosesCount += ($totalArsipUser - $sumKnown);
                }

                $selesaiPct = $totalArsipUser > 0 ? round(($selesaiCount / $totalArsipUser) * 100, 1) : 0;
                $prosesPct  = $totalArsipUser > 0 ? round(($prosesCount / $totalArsipUser) * 100, 1) : 0;
                $gagalPct   = $totalArsipUser > 0 ? round(($gagalCount / $totalArsipUser) * 100, 1) : 0;

                $data['status_arsip_arsiparis'] = [
                    'selesai'     => $selesaiCount,
                    'proses'      => $prosesCount,
                    'gagal'       => $gagalCount,
                    'total'       => $totalArsipUser,
                    'selesai_pct' => $selesaiPct,
                    'proses_pct'  => $prosesPct,
                    'gagal_pct'   => $gagalPct,
                ];

                // 3. Tren Pengunggahan Arsip (Bar Chart) dari Database
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

                // Ambil daftar SPT yang ditugaskan kepada Arsiparis
                $data['recent_assigned_spt'] = $userId ? $sptModel->select('spt.*, spt_assignments.status as assignment_status')
                    ->join('spt_assignments', 'spt_assignments.id_spt = spt.id_spt')
                    ->where('spt_assignments.id_user', $userId)
                    ->orderBy('spt.created_at', 'DESC')
                    ->limit(5)
                    ->findAll() : [];
                break;
        }

        $data['stats'] = $stats;

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

            // Tentukan icon dan colorClass berdasarkan modul dan action
            if (strpos($module, 'user') !== false) {
                if (strpos($action, 'create') !== false || strpos($action, 'insert') !== false || strpos($action, 'tambah') !== false) {
                    $icon = 'bi-person-plus';
                    $colorClass = 'icon-green';
                } elseif (strpos($action, 'delete') !== false || strpos($action, 'hapus') !== false) {
                    $icon = 'bi-person-dash';
                    $colorClass = 'icon-rose';
                } elseif (strpos($action, 'switch') !== false) {
                    $icon = 'bi-arrow-left-right';
                    $colorClass = 'icon-amber';
                } else {
                    $icon = 'bi-person-gear';
                    $colorClass = 'icon-blue';
                }
            } elseif (strpos($module, 'penilaian') !== false) {
                if (strpos($action, 'revisi') !== false || strpos($desc, 'revisi') !== false) {
                    $icon = 'bi-arrow-repeat';
                    $colorClass = 'icon-amber';
                } else {
                    $icon = 'bi-check2-circle';
                    $colorClass = 'icon-purple';
                }
            } elseif (strpos($module, 'arsip') !== false) {
                if (strpos($action, 'verify') !== false || strpos($action, 'verifikasi') !== false || strpos($desc, 'verifikasi') !== false) {
                    $icon = 'bi-shield-check';
                    $colorClass = 'icon-green';
                } elseif (strpos($action, 'delete') !== false || strpos($action, 'hapus') !== false) {
                    $icon = 'bi-trash3';
                    $colorClass = 'icon-rose';
                } elseif (strpos($action, 'create') !== false || strpos($action, 'insert') !== false || strpos($action, 'unggah') !== false) {
                    $icon = 'bi-cloud-arrow-up';
                    $colorClass = 'icon-blue';
                } else {
                    $icon = 'bi-file-earmark-text';
                    $colorClass = 'icon-blue';
                }
            } elseif (strpos($module, 'spt') !== false) {
                if (strpos($action, 'assign') !== false || strpos($desc, 'menugaskan') !== false) {
                    $icon = 'bi-person-badge';
                    $colorClass = 'icon-amber';
                } elseif (strpos($action, 'delete') !== false || strpos($action, 'hapus') !== false) {
                    $icon = 'bi-trash3';
                    $colorClass = 'icon-rose';
                } else {
                    $icon = 'bi-file-text';
                    $colorClass = 'icon-amber';
                }
            } elseif (strpos($module, 'berita') !== false || strpos($module, 'acara') !== false) {
                $icon = 'bi-file-earmark-check';
                $colorClass = 'icon-rose';
            } elseif (strpos($module, 'opd') !== false) {
                $icon = 'bi-bank2';
                $colorClass = 'icon-green';
            } elseif (strpos($module, 'bidang') !== false) {
                $icon = 'bi-diagram-3';
                $colorClass = 'icon-green';
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
                'title'       => $title,
                'user'        => $userName,
                'icon'        => $icon,
                'color_class' => $colorClass,
                'date'        => date('d M Y', strtotime($act['created_at'])),
                'time'        => date('H:i', strtotime($act['created_at'])),
                'created_at'  => $act['created_at']
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
            'status' => 'success',
            'labels' => array_column($result, 'nama_opd'),
            'values' => array_map('intval', array_column($result, 'total_arsip')),
            'count'  => count($result),
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
}

