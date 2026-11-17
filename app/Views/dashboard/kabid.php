<!-- ============================================================== -->
<!-- DASHBOARD KHUSUS KEPALA BIDANG - SIAMA                         -->
<!-- ============================================================== -->

<link rel="stylesheet" href="<?= base_url('css/kabid-dashboard.css?v=' . (file_exists(FCPATH . 'css/kabid-dashboard.css') ? filemtime(FCPATH . 'css/kabid-dashboard.css') : time())) ?>">

<?php
// Ambil data status arsip dari controller (hasil query database sesuai OPD & Bidang)
$statusCounts = $status_arsip_kabid ?? [
    'selesai'        => 0,
    'dalam_proses'   => 0,
    'belum_diproses' => 0,
    'gagal'          => 0,
    'total'          => 0,
];

$totalArsip = (int)($statusCounts['total'] ?? 0);
$selesai    = (int)($statusCounts['selesai'] ?? 0);
$proses     = (int)($statusCounts['dalam_proses'] ?? 0);
$belum      = (int)($statusCounts['belum_diproses'] ?? 0);
$gagal      = (int)($statusCounts['gagal'] ?? 0);

$selesaiPct = $totalArsip > 0 ? round(($selesai / $totalArsip) * 100) : 0;
$prosesPct  = $totalArsip > 0 ? round(($proses / $totalArsip) * 100) : 0;
$belumPct   = $totalArsip > 0 ? round(($belum / $totalArsip) * 100) : 0;
$gagalPct   = $totalArsip > 0 ? round(($gagal / $totalArsip) * 100) : 0;

$selectedYear   = $selected_year ?? '2026';
$availableYears = $kabid_available_years ?? ['2026', '2025'];
$monthlyAll     = $kabid_monthly_data ?? [];
$monthly        = $monthly_arsip_kabid ?? [
    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
    'values' => $monthlyAll[$selectedYear] ?? array_fill(0, 12, 0),
    'tahun'  => $selectedYear
];

$actionItems = $action_items_kabid ?? [
    'spt_belum_ditugaskan' => 0,
    'arsip_menunggu'       => 0,
    'ba_menunggu'          => 0,
    'dokumen_revisi'       => 0,
    'total_action'         => 0,
];
$workflowList = $workflow_kabid_list ?? [];
?>

<div class="kabid-dashboard">
    <!-- ============================================================== -->
    <!-- 1. HEADING, STATISTIK UTAMA & PRIORITAS TINDAKAN KABID         -->
    <!-- ============================================================== -->
    <div class="mb-4">
        <h1 class="kabid-header-title">Selamat Datang, <?= esc($nama_lengkap ?? 'Kepala Bidang') ?>!</h1>
        <p class="kabid-header-desc">Ini adalah ringkasan statistik berdasarkan akses Anda sebagai Kepala Bidang<?= !empty($nama_bidang) ? ' - ' . esc($nama_bidang) : '' ?><?= !empty($nama_opd) ? ' (' . esc($nama_opd) . ')' : '' ?></p>
    </div>

    <!-- Summary Cards Utama (Total Arsip di Bidang & BA Menanti Verifikasi) -->
    <div class="row g-4 mb-3">
        <!-- Card 1: Total Arsip di Bidang Anda -->
        <div class="col-12 col-md-6">
            <div class="card kabid-stat-card h-100 border-0">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="kabid-stat-icon-blue">
                        <i class="bi bi-inboxes-fill"></i>
                    </div>
                    <div>
                        <div class="kabid-stat-label">Total Arsip di Bidang Anda</div>
                        <div class="kabid-stat-value">
                            <?= number_format((int)($stats['total_arsip_bidang'] ?? $totalArsip), 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Berita Acara Menanti Verifikasi -->
        <div class="col-12 col-md-6">
            <div class="card kabid-stat-card h-100 border-0">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="kabid-stat-icon-green">
                        <i class="bi bi-file-earmark-check-fill"></i>
                    </div>
                    <div>
                        <div class="kabid-stat-label">Berita Acara Menanti Verifikasi</div>
                        <div class="kabid-stat-value">
                            <?= number_format((int)($stats['ba_menanti_kabid'] ?? 0), 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pekerjaan yang Membutuhkan Tindakan Kepala Bidang (Prioritas Aksi) -->
    <div class="card border shadow-none mb-4 kabid-card" style="border-radius: 14px; border-color: #E5E7EB; background-color: #FFFFFF;">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-diamond text-warning fs-5"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Pekerjaan yang Membutuhkan Tindakan Anda</h6>
                        <span class="text-secondary small">Prioritaskan penugasan arsiparis, verifikasi hasil pekerjaan alih media, dan pemeriksaan berita acara.</span>
                    </div>
                </div>
                <?php if (($actionItems['total_action'] ?? 0) > 0) : ?>
                    <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                        <?= $actionItems['total_action'] ?> Butuh Tindakan
                    </span>
                <?php endif; ?>
            </div>

            <!-- 4 Action Priority Cards -->
            <div class="kabid-action-grid">
                <!-- Aksi 1: SPT Belum Ditugaskan -->
                <a href="<?= base_url('/spt') ?>" class="kabid-action-card card-amber" title="Lihat SPT yang belum ditugaskan ke Arsiparis">
                    <div class="kabid-action-card-left">
                        <div class="kabid-action-icon-box">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <div class="kabid-action-content">
                            <span class="kabid-action-label">SPT Belum Ditugaskan</span>
                            <span class="kabid-action-value"><?= number_format($actionItems['spt_belum_ditugaskan'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right kabid-action-arrow"></i>
                </a>

                <!-- Aksi 2: Hasil Alih Media Menunggu Verifikasi -->
                <a href="<?= base_url('/arsip') ?>" class="kabid-action-card card-blue" title="Periksa hasil alih media yang menunggu verifikasi">
                    <div class="kabid-action-card-left">
                        <div class="kabid-action-icon-box">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                        <div class="kabid-action-content">
                            <span class="kabid-action-label">Arsip Menunggu Verifikasi</span>
                            <span class="kabid-action-value"><?= number_format($actionItems['arsip_menunggu'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right kabid-action-arrow"></i>
                </a>

                <!-- Aksi 3: Berita Acara Menunggu Pemeriksaan -->
                <a href="<?= base_url('/berita-acara') ?>" class="kabid-action-card card-emerald" title="Periksa Berita Acara yang menunggu persetujuan Anda">
                    <div class="kabid-action-card-left">
                        <div class="kabid-action-icon-box">
                            <i class="bi bi-file-earmark-check-fill"></i>
                        </div>
                        <div class="kabid-action-content">
                            <span class="kabid-action-label">BA Menunggu Pemeriksaan</span>
                            <span class="kabid-action-value"><?= number_format($actionItems['ba_menunggu'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right kabid-action-arrow"></i>
                </a>

                <!-- Aksi 4: Dokumen Perlu Perbaikan (Revisi) -->
                <a href="<?= base_url('/berita-acara') ?>" class="kabid-action-card card-rose" title="Pantau berkas yang dikembalikan untuk perbaikan">
                    <div class="kabid-action-card-left">
                        <div class="kabid-action-icon-box">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </div>
                        <div class="kabid-action-content">
                            <span class="kabid-action-label">Dokumen Perlu Perbaikan</span>
                            <span class="kabid-action-value"><?= number_format($actionItems['dokumen_revisi'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right kabid-action-arrow"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 2. CONTENT SECTION: CHARTS DALAM DUA KOLOM BERDAMPINGAN        -->
    <!-- ============================================================== -->
    <div class="row g-4 mb-4">
        <!-- Card Kiri: Status Arsip di Bidang Anda (Donut Chart) -->
        <div class="col-12 col-lg-6">
            <div class="card kabid-card h-100 border-0">
                <div class="kabid-card-header">
                    <div class="kabid-card-title-group">
                        <i class="bi bi-file-earmark-text kabid-card-icon"></i>
                        <h2 class="kabid-card-title">Status Arsip di Bidang Anda</h2>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="kabid-donut-wrapper">
                        <!-- Canvas Donut -->
                        <div class="donut-chart-container">
                            <canvas id="kabidDonutChart" width="160" height="160"></canvas>
                            <div class="donut-center-text">
                                <span class="donut-number"><?= $totalArsip ?></span>
                                <span class="donut-label">Total Arsip</span>
                            </div>
                        </div>

                        <!-- Legend Kanan -->
                        <div class="kabid-legend-list">
                            <!-- Selesai -->
                            <div class="kabid-legend-item">
                                <div class="kabid-legend-left">
                                    <span class="kabid-legend-dot dot-green"></span>
                                    <span class="kabid-legend-name">Selesai</span>
                                </div>
                                <div class="kabid-legend-right">
                                    <span class="kabid-legend-count"><?= $selesai ?></span>
                                    <span class="kabid-legend-pct"><?= $selesaiPct ?>%</span>
                                </div>
                            </div>

                            <!-- Dalam Proses -->
                            <div class="kabid-legend-item">
                                <div class="kabid-legend-left">
                                    <span class="kabid-legend-dot dot-blue"></span>
                                    <span class="kabid-legend-name">Dalam Proses</span>
                                </div>
                                <div class="kabid-legend-right">
                                    <span class="kabid-legend-count"><?= $proses ?></span>
                                    <span class="kabid-legend-pct"><?= $prosesPct ?>%</span>
                                </div>
                            </div>

                            <!-- Belum Diproses -->
                            <div class="kabid-legend-item">
                                <div class="kabid-legend-left">
                                    <span class="kabid-legend-dot dot-amber"></span>
                                    <span class="kabid-legend-name">Belum Diproses</span>
                                </div>
                                <div class="kabid-legend-right">
                                    <span class="kabid-legend-count"><?= $belum ?></span>
                                    <span class="kabid-legend-pct"><?= $belumPct ?>%</span>
                                </div>
                            </div>

                            <!-- Gagal / Ditolak -->
                            <div class="kabid-legend-item">
                                <div class="kabid-legend-left">
                                    <span class="kabid-legend-dot dot-red"></span>
                                    <span class="kabid-legend-name">Gagal / Ditolak</span>
                                </div>
                                <div class="kabid-legend-right">
                                    <span class="kabid-legend-count"><?= $gagal ?></span>
                                    <span class="kabid-legend-pct"><?= $gagalPct ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Kanan: Arsip per Bulan (Bar Chart) -->
        <div class="col-12 col-lg-6">
            <div class="card kabid-card h-100 border-0">
                <div class="kabid-card-header">
                    <div class="kabid-card-title-group">
                        <i class="bi bi-calendar-event kabid-card-icon"></i>
                        <h2 class="kabid-card-title" id="kabidChartTitle">Arsip per Bulan (<?= esc($selectedYear) ?>)</h2>
                    </div>
                    <div>
                        <select class="form-select form-select-sm kabid-year-select" id="kabidYearSelect" aria-label="Pilih Tahun">
                            <?php foreach ($availableYears as $yr) : ?>
                                <option value="<?= esc($yr) ?>" <?= (string)$yr === (string)$selectedYear ? 'selected' : '' ?>><?= esc($yr) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="kabid-bar-wrapper">
                        <canvas id="kabidBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 3. MONITORING PENUGASAN DAN PROSES ALIH MEDIA (SPT & ARSIPARIS) -->
    <!-- ============================================================== -->
    <div class="card border shadow-none mb-4 kabid-card" id="monitoringPenugasanKabid" style="border-radius: 14px; border-color: #E5E7EB; background-color: #FFFFFF;">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2" style="font-size: 16px;">
                        <i class="bi bi-diagram-3 text-primary"></i>
                        <span>Monitoring Penugasan dan Proses Alih Media</span>
                        <?php if (!empty($workflowList)) : ?>
                            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 fw-bold" style="font-size: 11px;">
                                <?= count($workflowList) ?> Dokumen SPT
                            </span>
                        <?php endif; ?>
                    </h5>
                    <p class="text-secondary small mb-0">Pantau penugasan Arsiparis, kondisi pekerjaan, dan perkembangan alur alih media dokumen.</p>
                </div>
            </div>

            <!-- Toolbar Filter Monitoring Penugasan Kabid -->
            <div class="workflow-toolbar mb-3 p-3 bg-light rounded-3 border">
                <div class="workflow-search-box flex-grow-1">
                    <i class="bi bi-search"></i>
                    <input type="text" id="kabidWorkflowSearchInput" class="form-control" placeholder="Cari nomor SPT atau nama arsiparis...">
                </div>

                <div class="filter-dropdown-wrapper">
                    <select id="kabidWorkflowFilter" class="dashboard-select" aria-label="Filter Kondisi Proses">
                        <option value="all">Semua Kondisi Proses</option>
                        <option value="menunggu_penugasan">Menunggu Penugasan Arsiparis</option>
                        <option value="sudah_ditugaskan">Sudah Ditugaskan</option>
                        <option value="sedang_diproses">Sedang Diproses</option>
                        <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                <button type="button" id="btnResetKabidWorkflow" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" style="height: 34px; font-size: 12px; border-radius: 8px;">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </div>

            <!-- Tabel Monitoring Penugasan -->
            <div class="table-responsive">
                <table class="table table-workflow" id="kabidWorkflowTable">
                    <thead>
                        <tr>
                            <th class="col-wf-nomor">Nomor SPT</th>
                            <th class="col-wf-tgl text-center">Tanggal SPT</th>
                            <th class="col-wf-petugas">Nama Arsiparis yang Ditugaskan</th>
                            <th class="col-wf-arsip text-center">Jumlah Arsip</th>
                            <th class="col-wf-tahap">Tahapan Alur Saat Ini</th>
                            <th class="col-wf-status text-center">Status Pekerjaan</th>
                            <th class="col-wf-update text-center">Pembaruan Terakhir</th>
                            <th class="col-wf-aksi text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="kabidWorkflowTableBody">
                        <?php if (!empty($workflowList)) : ?>
                            <?php foreach ($workflowList as $wf) : ?>
                                <tr data-kondisi="<?= esc($wf['kondisi_proses'] ?? 'menunggu_penugasan') ?>" 
                                    data-search="<?= strtolower(esc($wf['nomor_spt'] . ' ' . ($wf['arsiparis_assigned'] ?? '') . ' ' . $wf['status_proses'])) ?>">
                                    <td class="col-wf-nomor">
                                        <div class="wf-spt-box">
                                            <i class="bi bi-file-earmark-text wf-spt-icon"></i>
                                            <span class="wf-spt-text"><?= esc($wf['nomor_spt']) ?></span>
                                        </div>
                                    </td>
                                    <td class="col-wf-tgl text-center">
                                        <span class="wf-date-badge"><?= !empty($wf['tanggal_spt']) ? date('d M Y', strtotime($wf['tanggal_spt'])) : '-' ?></span>
                                    </td>
                                    <td class="col-wf-petugas">
                                        <?php if (!empty($wf['arsiparis_assigned'])) : ?>
                                            <div class="wf-petugas-card">
                                                <div class="wf-petugas-name" title="<?= esc($wf['arsiparis_assigned']) ?>">
                                                    <i class="bi bi-person-fill text-muted opacity-75 me-1"></i>
                                                    <span><?= esc($wf['arsiparis_assigned']) ?></span>
                                                </div>
                                                <div class="wf-petugas-role">Arsiparis Pelaksana</div>
                                            </div>
                                        <?php else : ?>
                                            <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                                                <i class="bi bi-exclamation-circle"></i> Belum Ditugaskan
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="col-wf-arsip text-center">
                                        <span class="wf-count-badge">
                                            <?= $wf['total_arsip'] ?> Berkas
                                        </span>
                                    </td>
                                    <td class="col-wf-tahap">
                                        <?php if ($wf['status_proses'] === 'Selesai') : ?>
                                            <span class="workflow-step-pill step-selesai" title="<?= esc($wf['tahap_full']) ?>">
                                                <span class="step-num"><i class="bi bi-check-lg"></i></span>
                                                <span>Selesai</span>
                                            </span>
                                        <?php else : ?>
                                            <span class="workflow-step-pill step-tahap-<?= $wf['tahap_num'] ?>" title="<?= esc($wf['tahap_full']) ?>">
                                                <span class="step-num"><?= $wf['tahap_num'] ?></span>
                                                <span><?= esc($wf['tahap_label']) ?></span>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="col-wf-status text-center">
                                        <span class="wf-status-badge <?= esc($wf['status_class']) ?>">
                                            <span class="wf-status-dot"></span>
                                            <span class="wf-status-text"><?= esc($wf['status_proses']) ?></span>
                                        </span>
                                    </td>
                                    <td class="col-wf-update text-center">
                                        <?php if (!empty($wf['last_updated']) && $wf['last_updated'] !== '-') : ?>
                                            <?php 
                                                $timeParts = explode(',', $wf['last_updated']);
                                                $wfDate = trim($timeParts[0] ?? $wf['last_updated']);
                                                $wfTime = isset($timeParts[1]) ? trim($timeParts[1]) : '';
                                            ?>
                                            <div class="wf-time-box">
                                                <span class="wf-time-date"><?= esc($wfDate) ?></span>
                                                <?php if (!empty($wfTime)) : ?>
                                                    <span class="wf-time-hour"><?= esc($wfTime) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php else : ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="col-wf-aksi text-center">
                                        <a href="<?= base_url('/spt/detail/' . $wf['id_spt']) ?>" class="btn-wf-detail" title="Lihat Detail SPT &amp; Penugasan">
                                            <i class="bi bi-eye"></i>
                                            <span>Detail</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr id="emptyKabidWorkflowRow">
                                <td colspan="8" class="text-center text-muted py-4">Belum ada dokumen alih media atau SPT yang tercatat di lingkungan Anda.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div id="kabidWorkflowNoMatch" class="p-4 text-center text-muted d-none">
                <i class="bi bi-search fs-2 d-block mb-1 text-secondary opacity-50"></i>
                <span>Tidak ada dokumen yang sesuai dengan filter pencarian.</span>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 4. BERITA ACARA MENANTI VERIFIKASI KABID                       -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-12">
            <div class="card kabid-card border-0">
                <div class="kabid-card-header">
                    <div class="kabid-card-title-group">
                        <i class="bi bi-clock kabid-card-icon"></i>
                        <h2 class="kabid-card-title">Berita Acara Menanti Verifikasi Kabid</h2>
                    </div>
                    <a href="<?= base_url('/berita-acara?status=Draf_Kabid') ?>" class="kabid-link-all">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table kabid-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">Nomor</th>
                                    <th>Nomor Berita Acara</th>
                                    <th>Tanggal</th>
                                    <th>Pelaksana</th>
                                    <th>Status</th>
                                    <th class="text-center" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($ba_menanti_list)) : ?>
                                    <?php $no = 1; foreach ($ba_menanti_list as $row) : ?>
                                        <tr>
                                            <td class="text-secondary"><?= $no++ ?></td>
                                            <td class="fw-semibold text-dark"><?= esc($row['nomor_ba']) ?></td>
                                            <td class="text-secondary"><?= esc(date('d/m/Y', strtotime($row['tanggal_ba'] ?? 'now'))) ?></td>
                                            <td><?= esc($row['nama_pelaksana'] ?? '-') ?></td>
                                            <td>
                                                <span class="badge rounded-pill bg-warning-subtle text-warning fw-semibold px-2 py-1" style="font-size: 11px;">
                                                    <?= esc($row['status_persetujuan'] ?? 'Draf_Kabid') ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('/berita-acara/detail/' . $row['id_berita_acara']) ?>" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 12px; border-radius: 6px;">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6" class="p-0 border-0">
                                            <div class="kabid-empty-box">
                                                <div class="kabid-empty-icon mb-2">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                </div>
                                                <div class="kabid-empty-title">Tidak ada data</div>
                                                <p class="kabid-empty-desc">Saat ini belum ada berita acara yang menunggu verifikasi Anda.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT INISIALISASI GRAFIK CHART.JS & FILTER WORKFLOW KABID -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Donut Chart: Status Arsip di Bidang Anda
    const donutEl = document.getElementById('kabidDonutChart');
    if (donutEl && typeof Chart !== 'undefined') {
        const totalCount = <?= (int)$totalArsip ?>;
        const hasData = totalCount > 0;

        new Chart(donutEl, {
            type: 'doughnut',
            data: {
                labels: hasData 
                    ? ['Selesai', 'Dalam Proses', 'Belum Diproses', 'Gagal / Ditolak'] 
                    : ['Belum Ada Arsip'],
                datasets: [{
                    data: hasData 
                        ? [<?= (int)$selesai ?>, <?= (int)$proses ?>, <?= (int)$belum ?>, <?= (int)$gagal ?>]
                        : [1],
                    backgroundColor: hasData 
                        ? ['#10B981', '#1E60EC', '#F59E0B', '#EF4444']
                        : ['#E2E8F0'],
                    borderWidth: 0,
                    hoverOffset: hasData ? 4 : 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: hasData,
                        backgroundColor: '#0F172A',
                        titleFont: { family: 'Poppins', size: 12 },
                        bodyFont: { family: 'Poppins', size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const total = <?= (int)$totalArsip ?>;
                                const value = context.raw || 0;
                                const pct = total > 0 ? Math.round((value / total) * 100) : 0;
                                return ' ' + context.label + ': ' + value + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Bar Chart: Arsip per Bulan
    const barEl = document.getElementById('kabidBarChart');
    if (barEl && typeof Chart !== 'undefined') {
        const barLabels = <?= json_encode($monthly['labels'] ?? []) ?>;
        let barValues = <?= json_encode($monthly['values'] ?? []) ?>;
        const monthlyDataByYear = <?= json_encode($monthlyAll ?? []) ?>;

        const maxInit = Math.max(...barValues, 0);
        const yMaxInit = maxInit > 3 ? maxInit + 1 : 3;

        const barChart = new Chart(barEl, {
            type: 'bar',
            data: {
                labels: barLabels,
                datasets: [{
                    label: 'Jumlah Arsip',
                    data: barValues,
                    backgroundColor: '#1E60EC',
                    borderRadius: 4,
                    maxBarThickness: 18,
                    hoverBackgroundColor: '#1752CE'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        titleFont: { family: 'Poppins', size: 12 },
                        bodyFont: { family: 'Poppins', size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.parsed.y + ' Arsip';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: { family: 'Poppins', size: 11 },
                            color: '#64748B'
                        }
                    },
                    y: {
                        min: 0,
                        max: yMaxInit,
                        ticks: {
                            stepSize: 1,
                            font: { family: 'Poppins', size: 11 },
                            color: '#64748B'
                        },
                        grid: {
                            color: '#F1F5F9',
                            drawBorder: false
                        },
                        border: {
                            display: false
                        }
                    }
                }
            }
        });

        // Interaktivitas Dropdown Tahun (Dinamis dari Database)
        const yearSelect = document.getElementById('kabidYearSelect');
        const chartTitleEl = document.getElementById('kabidChartTitle');

        if (yearSelect) {
            yearSelect.addEventListener('change', function() {
                const selYear = this.value;
                if (chartTitleEl) {
                    chartTitleEl.textContent = 'Arsip per Bulan (' + selYear + ')';
                }

                // Ambil data bulan untuk tahun terpilih
                let newVals = monthlyDataByYear[selYear];
                if (newVals && Array.isArray(newVals)) {
                    updateBarChart(newVals);
                } else {
                    // Fallback AJAX ke endpoint kabid-chart-data
                    fetch('<?= base_url('dashboard/kabid-chart-data?year=') ?>' + encodeURIComponent(selYear))
                        .then(r => r.json())
                        .then(data => {
                            if (data && data.values) {
                                updateBarChart(data.values);
                            }
                        })
                        .catch(err => console.error('Gagal memuat data grafik kabid:', err));
                }
            });

            function updateBarChart(values) {
                barChart.data.datasets[0].data = values;
                const maxVal = Math.max(...values, 0);
                barChart.options.scales.y.max = maxVal > 3 ? maxVal + 1 : 3;
                barChart.update();
            }
        }
    }

    // 3. Filter Interaktif Monitoring Penugasan Kabid
    const searchInput = document.getElementById('kabidWorkflowSearchInput');
    const filterSelect = document.getElementById('kabidWorkflowFilter');
    const resetBtn = document.getElementById('btnResetKabidWorkflow');
    const tableRows = document.querySelectorAll('#kabidWorkflowTableBody tr');
    const noMatchEl = document.getElementById('kabidWorkflowNoMatch');

    function applyKabidWorkflowFilters() {
        const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const kondisi = filterSelect ? filterSelect.value : 'all';

        let visibleCount = 0;

        tableRows.forEach(row => {
            if (row.id === 'emptyKabidWorkflowRow') return;

            const rowSearch = (row.getAttribute('data-search') || '').toLowerCase();
            const rowKondisi = row.getAttribute('data-kondisi') || '';

            const matchSearch = !query || rowSearch.includes(query);
            const matchKondisi = kondisi === 'all' || rowKondisi === kondisi;

            if (matchSearch && matchKondisi) {
                row.classList.remove('d-none');
                visibleCount++;
            } else {
                row.classList.add('d-none');
            }
        });

        if (noMatchEl) {
            if (visibleCount === 0 && tableRows.length > 0 && tableRows[0].id !== 'emptyKabidWorkflowRow') {
                noMatchEl.classList.remove('d-none');
            } else {
                noMatchEl.classList.add('d-none');
            }
        }
    }

    if (searchInput) searchInput.addEventListener('input', applyKabidWorkflowFilters);
    if (filterSelect) filterSelect.addEventListener('change', applyKabidWorkflowFilters);
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (filterSelect) filterSelect.value = 'all';
            applyKabidWorkflowFilters();
        });
    }
});
</script>
