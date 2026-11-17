<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Dashboard - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Flash Message: Error -->
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
        <div class="fw-medium small">
            <?= session()->getFlashdata('error') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Flash Message: Success -->
<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
        <div class="fw-medium small">
            <?= session()->getFlashdata('success') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (in_array($nama_role, ['Admin_OPD', 'Administrator OPD'])) : ?>
    <?= $this->include('dashboard/admin_opd') ?>
<?php elseif (in_array($nama_role, ['Pimpinan', 'Kepala_Dinas', 'Kepala Dinas'])) : ?>
    <?= $this->include('dashboard/pimpinan') ?>
<?php elseif (in_array($nama_role, ['Kepala_Bidang', 'Kepala Bidang', 'Kabid'])) : ?>
    <?= $this->include('dashboard/kabid') ?>
<?php elseif ($nama_role === 'Arsiparis') : ?>
    <?= $this->include('dashboard/arsiparis') ?>
<?php else : ?>

<!-- Header Khusus Admin Pemkab -->
<div class="dashboard-header mb-4">
    <h1 class="h3 fw-bold text-dark mb-1" style="font-size: 24px; letter-spacing: -0.3px;">Selamat Datang, <?= esc($nama_lengkap ?? 'Administrator Pemkab') ?>!</h1>
    <p class="text-secondary mb-0" style="font-size: 14px;">Administrator Pemerintah Kabupaten &bull; Pantau ringkasan statistik dan aktivitas sistem alih media arsip se-kabupaten.</p>
</div>

<!-- Statistic Cards: Row 1 -->
<?php if ($nama_role == 'Admin_Pemkab' || empty($nama_role)) : ?>
    <div class="stats-grid">
        <!-- Card 1: Total OPD -->
        <div class="stat-card card-blue">
            <div class="stat-icon-box">
                <i class="bi bi-bank2"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total OPD</div>
                <div class="stat-value"><?= number_format($stats['total_opd'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>

        <!-- Card 2: Total Pengguna -->
        <div class="stat-card card-green">
            <div class="stat-icon-box">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Pengguna</div>
                <div class="stat-value"><?= number_format($stats['total_users'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>

        <!-- Card 3: Total Arsip Global -->
        <div class="stat-card card-purple">
            <div class="stat-icon-box">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Arsip Global</div>
                <div class="stat-value"><?= number_format($stats['total_arsip'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>

        <!-- Card 4: Total SPT Global -->
        <div class="stat-card card-amber">
            <div class="stat-icon-box">
                <i class="bi bi-file-text-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total SPT Global</div>
                <div class="stat-value"><?= number_format($stats['total_spt'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>

        <!-- Card 5: Total Berita Acara Global -->
        <div class="stat-card card-rose">
            <div class="stat-icon-box">
                <i class="bi bi-file-earmark-check-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Berita Acara Global</div>
                <div class="stat-value"><?= number_format($stats['total_berita_acara'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Main Stack: Pending Work, Progress & Chart, Workflow Monitoring, Recent Activity -->
<div class="dashboard-stack">

    <!-- Panel 1: Ringkasan Pekerjaan Belum Selesai (Butuh Tindak Lanjut) -->
    <div class="panel-card pending-work-panel">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div class="panel-title-block">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="panel-title">Ringkasan Pekerjaan Tertunda &amp; Butuh Tindak Lanjut</h2>
                    <?php if (!empty($pending_work_summary)) : ?>
                        <span class="pending-work-badge-count">
                            <i class="bi bi-clock-history"></i> <?= count($pending_work_summary) ?> OPD Berjalan
                        </span>
                    <?php endif; ?>
                </div>
                <div class="panel-title-accent"></div>
            </div>
            <span class="text-secondary small">Menampilkan daftar OPD dengan proses SPT, alih media, atau verifikasi yang masih berjalan.</span>
        </div>

        <?php if (!empty($pending_work_summary)) : ?>
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Perangkat Daerah (OPD)</th>
                            <th class="text-center">SPT Aktif</th>
                            <th class="text-center">Menunggu Verifikasi</th>
                            <th class="text-center">Total Belum Selesai</th>
                            <th>Tahapan yang Sedang Tertunda / Berjalan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_work_summary as $item) : ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($item['nama_opd']) ?></div>
                                    <small class="text-muted"><?= esc($item['kode_opd']) ?></small>
                                </td>
                                <td class="text-center">
                                    <?php if ($item['active_spt'] > 0) : ?>
                                        <span class="badge-status badge-status-blue fw-bold"><?= $item['active_spt'] ?> SPT</span>
                                    <?php else : ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($item['menunggu_verifikasi'] > 0) : ?>
                                        <span class="badge-status badge-status-amber fw-bold"><?= $item['menunggu_verifikasi'] ?> <?= esc($item['menunggu_verifikasi_label'] ?? 'Arsip') ?></span>
                                    <?php else : ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-light text-dark border px-2.5 py-1 fw-bold"><?= $item['total_pending'] ?> Pekerjaan</span>
                                </td>
                                <td>
                                    <div class="pending-tahapan-list d-flex flex-wrap gap-2 align-items-center" style="gap: 8px 10px;">
                                        <?php foreach ($item['tahapan_list'] as $thp) : ?>
                                            <span class="badge-status <?= esc($thp['badge']) ?>">
                                                <?= esc($thp['label']) ?> (<?= $thp['count'] ?>)
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="#monitoringWorkflow" class="btn-dashboard-action filter-opd-quick" data-opd-id="<?= esc($item['id_opd']) ?>" title="Pantau Workflow OPD ini">
                                        <i class="bi bi-diagram-3"></i> Pantau Workflow
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <div class="p-4 text-center text-muted bg-light rounded-3 border">
                <i class="bi bi-check2-circle text-success fs-1 d-block mb-2"></i>
                <span class="fw-medium">Seluruh proses pengarsipan dan alih media di seluruh OPD telah selesai dan tidak ada pekerjaan tertunda.</span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Panel 2: Distribusi & Progres Alih Media per OPD (Full Width) -->
    <div class="panel-card chart-panel">
        <div class="chart-panel-header">
            <div class="panel-title-block">
                <h2 class="panel-title">Distribusi &amp; Progres Alih Media per OPD</h2>
                <div class="panel-title-accent"></div>
            </div>

            <!-- Filter Dropdowns (Semua OPD & Semua Periode) -->
            <div class="chart-filters">
                <div class="filter-dropdown-wrapper">
                    <select id="filterOpd" class="dashboard-select" aria-label="Filter OPD">
                        <option value="all">Semua OPD</option>
                        <?php if (!empty($all_opd)) : ?>
                            <?php foreach ($all_opd as $opd) : ?>
                                <option value="<?= esc($opd['id_opd']) ?>">
                                    <?= esc($opd['nama_opd']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="filter-dropdown-wrapper">
                    <i class="bi bi-calendar3 filter-select-icon"></i>
                    <select id="filterPeriode" class="dashboard-select has-left-icon" aria-label="Filter Periode">
                        <option value="all">Semua Periode</option>
                        <?php if (!empty($all_periods)) : ?>
                            <?php foreach ($all_periods as $per) : ?>
                                <option value="<?= esc($per) ?>"><?= esc($per) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Scrollable Chart Canvas Area -->
        <div class="chart-scroll-wrapper" id="chartScrollWrapper">
            <div id="chartLoadingOverlay" class="chart-loading-overlay d-none">
                <div class="spinner-border text-primary spinner-border-sm" role="status">
                    <span class="visually-hidden">Memuat...</span>
                </div>
                <span class="small text-muted ms-2">Memperbarui data...</span>
            </div>
            
            <div id="chartContainer" class="chart-inner-container">
                <canvas id="arsipOpdChart"></canvas>
            </div>
        </div>

        <!-- Sumbu X Label Footer -->
        <div class="chart-axis-footer mb-3">
            <span class="axis-label-text">Jumlah Arsip Terdaftar</span>
        </div>

        <!-- Pembatas & Sub-Tabel Rincian Progres Alih Media per OPD -->
        <div class="panel-divider"></div>

        <div class="panel-sub-header">
            <h3 class="panel-sub-title">
                <i class="bi bi-bar-chart-steps text-primary"></i>
                <span>Rincian Progres Alih Media per OPD</span>
            </h3>
            <span class="text-secondary small" id="progressSummaryCount">Menampilkan <?= count($opd_progress_list ?? []) ?> Perangkat Daerah</span>
        </div>

        <div class="table-responsive">
            <table class="table table-modern" id="tableOpdProgress">
                <thead>
                    <tr>
                        <th>Nama Perangkat Daerah (OPD)</th>
                        <th class="text-center">Total Arsip</th>
                        <th class="text-center">Selesai</th>
                        <th class="text-center">Diproses</th>
                        <th class="text-center">Menunggu Verifikasi</th>
                        <th style="min-width: 170px;">Progres Penyelesaian</th>
                    </tr>
                </thead>
                <tbody id="progressTableBody">
                    <?php if (!empty($opd_progress_list)) : ?>
                        <?php foreach ($opd_progress_list as $prog) : ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($prog['nama_opd']) ?></div>
                                    <small class="text-muted"><?= esc($prog['kode_opd']) ?></small>
                                </td>
                                <td class="text-center fw-bold text-dark"><?= number_format($prog['total_arsip'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <span class="badge-status badge-status-green"><?= $prog['selesai_count'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-status badge-status-blue"><?= $prog['proses_count'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-status badge-status-amber"><?= $prog['menunggu_count'] ?></span>
                                </td>
                                <td>
                                    <div class="opd-progress-wrapper">
                                        <div class="opd-progress-text">
                                            <span><?= $prog['persentase'] ?>% Selesai</span>
                                            <span><?= $prog['selesai_count'] ?>/<?= $prog['total_arsip'] ?></span>
                                        </div>
                                        <div class="opd-progress-track">
                                            <div class="opd-progress-fill <?= $prog['persentase'] >= 100 ? 'fill-completed' : ($prog['persentase'] == 0 ? 'fill-empty' : '') ?>" style="width: <?= $prog['persentase'] ?>%;"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">Tidak ada data untuk filter yang dipilih.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Panel 3: Monitoring Workflow Pengarsipan -->
    <div class="panel-card" id="monitoringWorkflow">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div class="panel-title-block">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="panel-title">Monitoring Workflow Pengarsipan</h2>
                    <?php if (!empty($workflow_monitoring_list)) : ?>
                        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                            <?= count($workflow_monitoring_list) ?> Dokumen Workflow
                        </span>
                    <?php endif; ?>
                </div>
                <div class="panel-title-accent"></div>
            </div>
            <span class="text-secondary small">Pantau posisi alur dokumen alih media dari penerbitan SPT hingga Berita Acara se-kabupaten.</span>
        </div>

        <!-- Toolbar Filter Workflow -->
        <div class="workflow-toolbar mb-3 p-3 bg-light rounded-3 border">
            <div class="workflow-search-box flex-grow-1">
                <i class="bi bi-search"></i>
                <input type="text" id="workflowSearchInput" class="form-control" placeholder="Cari nomor SPT, OPD, atau nama petugas...">
            </div>

            <div class="filter-dropdown-wrapper">
                <select id="workflowOpdFilter" class="dashboard-select" aria-label="Filter OPD Workflow">
                    <option value="all">Semua OPD</option>
                    <?php if (!empty($all_opd)) : ?>
                        <?php foreach ($all_opd as $opd) : ?>
                            <option value="<?= esc($opd['id_opd']) ?>"><?= esc($opd['nama_opd']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="filter-dropdown-wrapper">
                <select id="workflowTahapFilter" class="dashboard-select" aria-label="Filter Tahapan">
                    <option value="all">Semua Tahapan</option>
                    <option value="1">Tahap 1: Penerbitan SPT</option>
                    <option value="2">Tahap 2: Penugasan Arsiparis</option>
                    <option value="3">Tahap 3: Proses Alih Media</option>
                    <option value="4">Tahap 4: Verifikasi Kabid</option>
                    <option value="5">Tahap 5: Persetujuan Pimpinan</option>
                </select>
            </div>

            <div class="filter-dropdown-wrapper">
                <select id="workflowStatusFilter" class="dashboard-select" aria-label="Filter Status">
                    <option value="all">Semua Status</option>
                    <option value="Menunggu Penugasan Arsiparis">Menunggu Penugasan</option>
                    <option value="Menunggu Mulai Alih Media">Menunggu Alih Media</option>
                    <option value="Sedang Alih Media">Sedang Alih Media</option>
                    <option value="Menunggu Verifikasi Kabid">Menunggu Verifikasi Kabid</option>
                    <option value="Menunggu Verifikasi Arsip">Menunggu Verifikasi Arsip</option>
                    <option value="Menunggu TTD Pimpinan">Menunggu TTD Pimpinan</option>
                    <option value="Perbaikan Draf (Revisi)">Revisi</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>

            <button type="button" id="btnResetWorkflowFilter" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" style="height: 34px; font-size: 12px; border-radius: 8px;">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </div>

        <!-- Tabel Workflow Monitoring -->
        <div class="table-responsive">
            <table class="table table-workflow" id="workflowTable">
                <thead>
                    <tr>
                        <th class="col-wf-nomor">Nomor SPT</th>
                        <th class="col-wf-opd">Perangkat Daerah (OPD)</th>
                        <th class="col-wf-tgl text-center">Tanggal SPT</th>
                        <th class="col-wf-arsip text-center">Jumlah Arsip</th>
                        <th class="col-wf-tahap">Tahapan Alur Saat Ini</th>
                        <th class="col-wf-petugas">Petugas / Role Penanganan</th>
                        <th class="col-wf-status text-center">Status Proses</th>
                        <th class="col-wf-update text-center">Pembaruan Terakhir</th>
                        <th class="col-wf-aksi text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="workflowTableBody">
                    <?php if (!empty($workflow_monitoring_list)) : ?>
                        <?php foreach ($workflow_monitoring_list as $wf) : ?>
                            <tr data-opd-id="<?= esc($wf['id_opd']) ?>" 
                                data-tahap="<?= esc($wf['tahap_num']) ?>" 
                                data-status="<?= esc($wf['status_proses']) ?>"
                                data-search="<?= strtolower(esc($wf['nomor_spt'] . ' ' . $wf['nama_opd'] . ' ' . $wf['petugas'] . ' ' . $wf['status_proses'])) ?>">
                                <td class="col-wf-nomor">
                                    <div class="wf-spt-box">
                                        <i class="bi bi-file-earmark-text wf-spt-icon"></i>
                                        <span class="wf-spt-text"><?= esc($wf['nomor_spt']) ?></span>
                                    </div>
                                </td>
                                <td class="col-wf-opd">
                                    <div class="wf-opd-box" title="<?= esc($wf['nama_opd']) ?>">
                                        <span class="wf-opd-name"><?= esc($wf['nama_opd']) ?></span>
                                    </div>
                                </td>
                                <td class="col-wf-tgl text-center">
                                    <span class="wf-date-badge"><?= !empty($wf['tanggal_spt']) ? date('d M Y', strtotime($wf['tanggal_spt'])) : '-' ?></span>
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
                                <td class="col-wf-petugas">
                                    <div class="wf-petugas-card">
                                        <?php if (!empty($wf['has_specific_user']) && !empty($wf['petugas_name'])) : ?>
                                            <div class="wf-petugas-name" title="<?= esc($wf['petugas_name']) ?>">
                                                <i class="bi bi-person-fill text-muted opacity-75 me-1"></i>
                                                <span><?= esc($wf['petugas_name']) ?></span>
                                            </div>
                                            <div class="wf-petugas-role">
                                                <?= esc($wf['petugas_role']) ?>
                                            </div>
                                        <?php else : ?>
                                            <div class="wf-petugas-name" title="<?= esc($wf['petugas_role']) ?>">
                                                <i class="bi bi-person text-muted opacity-75 me-1"></i>
                                                <span><?= esc($wf['petugas_role']) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
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
                                    <a href="<?= base_url('/spt/detail/' . $wf['id_spt']) ?>" class="btn-wf-detail" title="Lihat Detail SPT &amp; Alur">
                                        <i class="bi bi-eye"></i>
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr id="emptyWorkflowRow">
                            <td colspan="9" class="text-center text-muted py-4">Belum ada dokumen alih media atau SPT yang tercatat.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div id="workflowNoMatch" class="p-4 text-center text-muted d-none">
            <i class="bi bi-search fs-2 d-block mb-1 text-secondary opacity-50"></i>
            <span>Tidak ada dokumen yang sesuai dengan filter pencarian.</span>
        </div>
    </div>

    <!-- Panel 4: Aktivitas Terbaru (Full Width) -->
    <div class="panel-card activity-panel">
        <div class="activity-panel-header">
            <div class="panel-title-block">
                <h2 class="panel-title">Aktivitas Terbaru</h2>
                <div class="panel-title-accent"></div>
            </div>

            <a href="<?= base_url('/audit-log/activity') ?>" class="activity-more-link">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="activity-scroll-wrapper">
            <div class="activity-list">
                <?php if (!empty($recent_activities)) : ?>
                    <?php foreach ($recent_activities as $act) : ?>
                        <div class="activity-item">
                            <div class="activity-left">
                                <div class="activity-icon <?= esc($act['color_class'] ?? 'icon-blue') ?>">
                                    <i class="bi <?= esc($act['icon'] ?? 'bi-file-earmark') ?>"></i>
                                </div>
                                <div class="activity-desc">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="activity-action" title="<?= esc($act['title'] ?? 'Aktivitas Sistem') ?>"><?= esc($act['title'] ?? 'Aktivitas Sistem') ?></span>
                                        <?php if (!empty($act['badge'])) : ?>
                                            <span class="badge-status <?= esc($act['badge_class'] ?? 'badge-status-blue') ?>" style="font-size: 10px; padding: 1px 6px;">
                                                <?= esc($act['badge']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="activity-user">Oleh <?= esc($act['user'] ?? 'Administrator Pemkab') ?></span>
                                </div>
                            </div>
                            <div class="activity-time">
                                <span class="activity-date"><?= esc($act['date']) ?></span>
                                <span class="activity-hour"><?= esc($act['time']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <!-- Sample Initial Activities when log is pristine -->
                    <div class="activity-item">
                        <div class="activity-left">
                            <div class="activity-icon icon-green">
                                <i class="bi bi-plus-lg"></i>
                            </div>
                            <div class="activity-desc">
                                <span class="activity-action">Pengguna baru ditambahkan</span>
                                <span class="activity-user">Oleh Administrator Pemkab</span>
                            </div>
                        </div>
                        <div class="activity-time">
                            <span class="activity-date"><?= date('d M Y') ?></span>
                            <span class="activity-hour"><?= date('H:i') ?></span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-left">
                            <div class="activity-icon icon-blue">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="activity-desc">
                                <span class="activity-action">Data arsip diperbarui</span>
                                <span class="activity-user">Oleh Admin OPD - Dinkes</span>
                            </div>
                        </div>
                        <div class="activity-time">
                            <span class="activity-date"><?= date('d M Y', strtotime('-1 day')) ?></span>
                            <span class="activity-hour">14:23</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-left">
                            <div class="activity-icon icon-amber">
                                <i class="bi bi-file-text"></i>
                            </div>
                            <div class="activity-desc">
                                <span class="activity-action">SPT baru dibuat</span>
                                <span class="activity-user">Nomor: SPT/2026/IX/028</span>
                            </div>
                        </div>
                        <div class="activity-time">
                            <span class="activity-date"><?= date('d M Y', strtotime('-1 day')) ?></span>
                            <span class="activity-hour">10:17</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-left">
                            <div class="activity-icon icon-purple">
                                <i class="bi bi-check2"></i>
                            </div>
                            <div class="activity-desc">
                                <span class="activity-action">Penilaian arsip selesai</span>
                                <span class="activity-user">Oleh Arsiparis - Disdikpora</span>
                            </div>
                        </div>
                        <div class="activity-time">
                            <span class="activity-date"><?= date('d M Y', strtotime('-2 day')) ?></span>
                            <span class="activity-hour">16:42</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-left">
                            <div class="activity-icon icon-rose">
                                <i class="bi bi-file-earmark-check"></i>
                            </div>
                            <div class="activity-desc">
                                <span class="activity-action">Berita acara ditandatangani</span>
                                <span class="activity-user">Nomor: BA/2026/IX/015</span>
                            </div>
                        </div>
                        <div class="activity-time">
                            <span class="activity-date"><?= date('d M Y', strtotime('-2 day')) ?></span>
                            <span class="activity-hour">09:28</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-left">
                            <div class="activity-icon icon-blue">
                                <i class="bi bi-person"></i>
                            </div>
                            <div class="activity-desc">
                                <span class="activity-action">Akun OPD diperbarui</span>
                                <span class="activity-user">Oleh Administrator Pemkab</span>
                            </div>
                        </div>
                        <div class="activity-time">
                            <span class="activity-date"><?= date('d M Y', strtotime('-3 day')) ?></span>
                            <span class="activity-hour">15:11</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('arsipOpdChart');
    const container = document.getElementById('chartContainer');
    const loadingOverlay = document.getElementById('chartLoadingOverlay');
    const filterOpd = document.getElementById('filterOpd');
    const filterPeriode = document.getElementById('filterPeriode');

    if (!ctx) return;

    let initialLabels = <?= json_encode($chart_labels ?? []) ?>;
    let initialValues = <?= json_encode($chart_values ?? []) ?>;

    /**
     * Helper function untuk membungkus nama OPD yang panjang menjadi multi-baris string array
     */
    function wrapLabel(label, maxCharsPerLine = 32) {
        if (!label) return '';
        if (typeof label !== 'string') return label;
        if (label.length <= maxCharsPerLine) return label;

        const words = label.split(' ');
        const lines = [];
        let currentLine = '';

        words.forEach(word => {
            const testLine = (currentLine + ' ' + word).trim();
            if (testLine.length <= maxCharsPerLine) {
                currentLine = testLine;
            } else {
                if (currentLine) lines.push(currentLine);
                currentLine = word;
            }
        });

        if (currentLine) lines.push(currentLine);
        return lines;
    }

    /**
     * Custom Plugin Chart.js untuk menggambar angka jumlah arsip di ujung kanan setiap bar
     */
    const valueLabelsPlugin = {
        id: 'valueLabelsPlugin',
        afterDatasetsDraw(chart) {
            const { ctx, data } = chart;
            ctx.save();
            ctx.font = '500 12.5px Poppins, sans-serif';
            ctx.fillStyle = '#172033';
            ctx.textAlign = 'left';
            ctx.textBaseline = 'middle';

            chart.getDatasetMeta(0).data.forEach((bar, index) => {
                const val = data.datasets[0].data[index];
                if (val !== undefined && val !== null) {
                    const x = bar.x + 8;
                    const y = bar.y;
                    ctx.fillText(val.toLocaleString('id-ID'), x, y);
                }
            });
            ctx.restore();
        }
    };

    /**
     * Hitung tinggi dinamis kontainer berdasarkan jumlah batang agar tidak bertumpuk
     */
    function updateContainerHeight(itemsCount) {
        if (!container) return;
        // Berikan ruang nyaman ~46px per item horizontal bar
        const targetHeight = Math.max(340, itemsCount * 46);
        container.style.height = targetHeight + 'px';
    }

    // Format label awal
    const formattedLabels = initialLabels.map(l => wrapLabel(l, 32));
    updateContainerHeight(formattedLabels.length);

    // Hitung suggested max x-axis
    const maxVal = initialValues.length ? Math.max(...initialValues, 0) : 0;
    const computedMax = maxVal === 0 ? 50 : Math.ceil(maxVal * 1.18);

    let myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: formattedLabels,
            datasets: [{
                data: initialValues,
                backgroundColor: '#1E60EC',
                hoverBackgroundColor: '#1752ce',
                borderRadius: 4,
                borderSkipped: false,
                barThickness: 24,
                maxBarThickness: 28,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    right: 48,
                    left: 4,
                    top: 6,
                    bottom: 6
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#0B1528',
                    titleFont: { family: 'Poppins', size: 12, weight: '600' },
                    bodyFont: { family: 'Poppins', size: 12 },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        title: function(items) {
                            if (!items.length) return '';
                            const rawLabel = initialLabels[items[0].dataIndex] || items[0].label;
                            return Array.isArray(rawLabel) ? rawLabel.join(' ') : rawLabel;
                        },
                        label: function(context) {
                            return 'Jumlah Arsip: ' + context.parsed.x.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    suggestedMax: computedMax,
                    grid: {
                        color: '#E5E7EB',
                        drawBorder: false,
                        lineWidth: 1
                    },
                    ticks: {
                        font: {
                            family: 'Poppins',
                            size: 11,
                            weight: '500'
                        },
                        color: '#667085',
                        padding: 6,
                        stepSize: computedMax <= 10 ? 2 : (computedMax <= 50 ? 10 : (computedMax <= 200 ? 25 : 50))
                    }
                },
                y: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            family: 'Poppins',
                            size: 11.5,
                            weight: '500'
                        },
                        color: '#334155',
                        padding: 12,
                        autoSkip: false
                    }
                }
            }
        },
        plugins: [valueLabelsPlugin]
    });

    /**
     * Helper Escape HTML untuk proteksi XSS pada dynamic rendering
     */
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    /**
     * Render ulang tabel ringkasan progres alih media per OPD saat filter berubah
     */
    function updateProgressTable(progressList) {
        const tbody = document.getElementById('progressTableBody');
        const countSpan = document.getElementById('progressSummaryCount');
        if (!tbody) return;

        if (!progressList || !progressList.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data untuk filter yang dipilih.</td></tr>';
            if (countSpan) countSpan.textContent = 'Menampilkan 0 Perangkat Daerah';
            return;
        }

        if (countSpan) countSpan.textContent = `Menampilkan ${progressList.length} Perangkat Daerah`;

        let html = '';
        progressList.forEach(item => {
            const pct = parseFloat(item.persentase || 0);
            const fillClass = pct >= 100 ? 'fill-completed' : (pct === 0 ? 'fill-empty' : '');
            html += `
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">${escapeHtml(item.nama_opd)}</div>
                        <small class="text-muted">${escapeHtml(item.kode_opd || '-')}</small>
                    </td>
                    <td class="text-center fw-bold text-dark">${Number(item.total_arsip).toLocaleString('id-ID')}</td>
                    <td class="text-center">
                        <span class="badge-status badge-status-green">${Number(item.selesai_count).toLocaleString('id-ID')}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge-status badge-status-blue">${Number(item.proses_count).toLocaleString('id-ID')}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge-status badge-status-amber">${Number(item.menunggu_count).toLocaleString('id-ID')}</span>
                    </td>
                    <td>
                        <div class="opd-progress-wrapper">
                            <div class="opd-progress-text">
                                <span>${pct}% Selesai</span>
                                <span>${item.selesai_count}/${item.total_arsip}</span>
                            </div>
                            <div class="opd-progress-track">
                                <div class="opd-progress-fill ${fillClass}" style="width: ${pct}%;"></div>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    /**
     * AJAX Fetch untuk memperbarui data grafik & tabel progres saat filter diubah
     */
    function fetchAndUpdateChart() {
        const opdVal = filterOpd ? filterOpd.value : 'all';
        const periodeVal = filterPeriode ? filterPeriode.value : 'all';

        if (loadingOverlay) loadingOverlay.classList.remove('d-none');

        const url = `<?= base_url('/dashboard/chart-data') ?>?opd=${encodeURIComponent(opdVal)}&periode=${encodeURIComponent(periodeVal)}`;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                initialLabels = res.labels || [];
                const newValues = res.values || [];
                const newFormattedLabels = initialLabels.map(l => wrapLabel(l, 32));

                updateContainerHeight(newFormattedLabels.length);

                const currentMax = newValues.length ? Math.max(...newValues, 0) : 0;
                const newComputedMax = currentMax === 0 ? 20 : Math.ceil(currentMax * 1.18);

                myChart.data.labels = newFormattedLabels;
                myChart.data.datasets[0].data = newValues;
                myChart.options.scales.x.suggestedMax = newComputedMax;
                myChart.options.scales.x.ticks.stepSize = newComputedMax <= 10 ? 2 : (newComputedMax <= 50 ? 10 : (newComputedMax <= 200 ? 25 : 50));
                
                myChart.resize();
                myChart.update();

                // Perbarui juga data tabel progres per OPD
                if (res.opd_progress) {
                    updateProgressTable(res.opd_progress);
                }
            }
        })
        .catch(err => {
            console.error('Gagal memuat data grafik:', err);
        })
        .finally(() => {
            if (loadingOverlay) loadingOverlay.classList.add('d-none');
        });
    }

    if (filterOpd) {
        filterOpd.addEventListener('change', fetchAndUpdateChart);
    }
    if (filterPeriode) {
        filterPeriode.addEventListener('change', fetchAndUpdateChart);
    }

    // =========================================================================
    // Filter Interaktif untuk Monitoring Workflow Pengarsipan
    // =========================================================================
    const wfSearch = document.getElementById('workflowSearchInput');
    const wfOpd = document.getElementById('workflowOpdFilter');
    const wfTahap = document.getElementById('workflowTahapFilter');
    const wfStatus = document.getElementById('workflowStatusFilter');
    const wfReset = document.getElementById('btnResetWorkflowFilter');
    const wfRows = document.querySelectorAll('#workflowTableBody tr');
    const wfNoMatch = document.getElementById('workflowNoMatch');

    function applyWorkflowFilters() {
        const searchVal = (wfSearch ? wfSearch.value : '').toLowerCase().trim();
        const opdVal = wfOpd ? wfOpd.value : 'all';
        const tahapVal = wfTahap ? wfTahap.value : 'all';
        const statusVal = wfStatus ? wfStatus.value : 'all';

        let visibleCount = 0;

        wfRows.forEach(row => {
            if (row.id === 'emptyWorkflowRow') return;

            const rowSearch = (row.getAttribute('data-search') || '').toLowerCase();
            const rowOpd = row.getAttribute('data-opd-id') || '';
            const rowTahap = row.getAttribute('data-tahap') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchSearch = !searchVal || rowSearch.includes(searchVal);
            const matchOpd = opdVal === 'all' || rowOpd === opdVal;
            const matchTahap = tahapVal === 'all' || rowTahap === tahapVal;
            const matchStatus = statusVal === 'all' || rowStatus === statusVal;

            if (matchSearch && matchOpd && matchTahap && matchStatus) {
                row.classList.remove('d-none');
                visibleCount++;
            } else {
                row.classList.add('d-none');
            }
        });

        if (wfNoMatch) {
            if (visibleCount === 0 && wfRows.length > 0 && wfRows[0].id !== 'emptyWorkflowRow') {
                wfNoMatch.classList.remove('d-none');
            } else {
                wfNoMatch.classList.add('d-none');
            }
        }
    }

    if (wfSearch) wfSearch.addEventListener('input', applyWorkflowFilters);
    if (wfOpd) wfOpd.addEventListener('change', applyWorkflowFilters);
    if (wfTahap) wfTahap.addEventListener('change', applyWorkflowFilters);
    if (wfStatus) wfStatus.addEventListener('change', applyWorkflowFilters);

    if (wfReset) {
        wfReset.addEventListener('click', function() {
            if (wfSearch) wfSearch.value = '';
            if (wfOpd) wfOpd.value = 'all';
            if (wfTahap) wfTahap.value = 'all';
            if (wfStatus) wfStatus.value = 'all';
            applyWorkflowFilters();
        });
    }

    // Quick filter dari tabel Ringkasan Pekerjaan Tertunda
    document.querySelectorAll('.filter-opd-quick').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const opdId = this.getAttribute('data-opd-id');
            if (opdId && wfOpd) {
                wfOpd.value = opdId;
                applyWorkflowFilters();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
