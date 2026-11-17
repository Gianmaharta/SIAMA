<!-- ============================================================== -->
<!-- DASHBOARD KHUSUS ADMINISTRATOR OPD - SIAMA                      -->
<!-- ============================================================== -->

<!-- 1. HEADER DASHBOARD -->
<div class="dashboard-header mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1" style="font-size: 24px; letter-spacing: -0.3px;">Selamat Datang, <?= esc($nama_lengkap ?? 'Administrator OPD') ?>!</h1>
            <p class="text-secondary mb-0" style="font-size: 14px;">Ringkasan statistik pengelolaan arsip dan pemantauan alur alih media di lingkungan <?= !empty($nama_opd) ? esc($nama_opd) : 'OPD Anda' ?>.</p>
        </div>
        <?php if (!empty($nama_opd)) : ?>
            <div class="d-inline-flex align-items-center gap-2 px-3 py-2 bg-white border rounded-pill shadow-xs">
                <i class="bi bi-building text-primary"></i>
                <span class="fw-semibold text-dark small"><?= esc($nama_opd) ?></span>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 2. STATISTIC CARDS (3 Cards Horizontal - Sesuai Komponen Dashboard Saat Ini) -->
<div class="row g-3 g-md-4 mb-4">
    <!-- Card 1: Total Pengguna di OPD Anda -->
    <div class="col-12 col-md-4">
        <div class="card border h-100 shadow-none opd-stat-card opd-dashboard-card" style="border-radius: 14px; border-color: #E5E7EB; background-color: #FFFFFF; overflow: hidden; transition: transform 0.2s ease, box-shadow 0.2s ease;">
            <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; background-color: #EFF6FF;">
                    <i class="bi bi-people fs-4 text-primary"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-secondary small mb-1" style="font-size: 13px; font-weight: 500;">Total Pengguna di OPD Anda</div>
                    <div class="fw-bold text-dark" style="font-size: 26px; line-height: 1;"><?= number_format($stats['total_users_opd'] ?? count($users_opd ?? []), 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Total Bidang di OPD Anda -->
    <div class="col-12 col-md-4">
        <div class="card border h-100 shadow-none opd-stat-card opd-dashboard-card" style="border-radius: 14px; border-color: #E5E7EB; background-color: #FFFFFF; overflow: hidden; transition: transform 0.2s ease, box-shadow 0.2s ease;">
            <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; background-color: #ECFDF5;">
                    <i class="bi bi-building fs-4" style="color: #10B981;"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-secondary small mb-1" style="font-size: 13px; font-weight: 500;">Total Bidang di OPD Anda</div>
                    <div class="fw-bold text-dark" style="font-size: 26px; line-height: 1;"><?= number_format($stats['total_bidang_opd'] ?? count($bidang_opd ?? []), 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Total Arsip di OPD Anda -->
    <div class="col-12 col-md-4">
        <div class="card border h-100 shadow-none opd-stat-card opd-dashboard-card" style="border-radius: 14px; border-color: #E5E7EB; background-color: #FFFFFF; overflow: hidden; transition: transform 0.2s ease, box-shadow 0.2s ease;">
            <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; background-color: #FAF5FF;">
                    <i class="bi bi-file-earmark-text fs-4" style="color: #8B5CF6;"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-secondary small mb-1" style="font-size: 13px; font-weight: 500;">Total Arsip di OPD Anda</div>
                    <div class="fw-bold text-dark" style="font-size: 26px; line-height: 1;"><?= number_format($stats['total_arsip_opd'] ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================================= -->
<!-- PENGGUNA OPD DAN BIDANG DI OPD ANDA (TETAP DUA KOLOM BERDAMPINGAN)                        -->
<!-- ========================================================================================= -->
<div class="row g-4 mb-4">
    <!-- Kolom 1: Pengguna OPD -->
    <div class="col-12 col-lg-6">
        <div class="card border h-100 shadow-none opd-dashboard-card" style="border-radius: 14px; border-color: #E5E7EB; background-color: #FFFFFF; overflow: hidden;">
            <div class="card-body p-0 d-flex flex-column">
                <!-- Header Card -->
                <div class="p-3 px-4 d-flex align-items-center justify-content-between border-bottom" style="border-color: #F1F5F9 !important;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-people text-primary fs-5"></i>
                        <span class="fw-bold text-dark" style="font-size: 15px;">Pengguna OPD</span>
                    </div>
                    <a href="<?= base_url('/users') ?>" class="text-primary text-decoration-none fw-semibold small d-flex align-items-center gap-1">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <!-- Table Pengguna -->
                <div class="table-responsive flex-grow-1">
                    <table class="table align-middle mb-0" style="font-size: 13.5px;">
                        <thead style="border-bottom: 1px solid #E5E7EB; background-color: #F8FAFC;">
                            <tr>
                                <th class="text-secondary fw-semibold ps-4 py-3" style="width: 50px;">No</th>
                                <th class="text-secondary fw-semibold py-3">Nama</th>
                                <th class="text-secondary fw-semibold py-3">Role</th>
                                <th class="text-secondary fw-semibold pe-4 py-3 text-center" style="width: 100px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users_opd)) : ?>
                                <?php $no = 1; foreach ($users_opd as $u) : ?>
                                    <tr style="border-bottom: 1px solid #F1F5F9;">
                                         <td class="ps-4 text-muted"><?= $no++ ?></td>
                                        <td class="fw-semibold text-dark"><?= esc($u['nama']) ?></td>
                                        <td class="text-secondary"><?= esc($u['nama_role'] ?? 'Pengguna') ?></td>
                                        <td class="pe-4 text-center">
                                            <?php if ((int)($u['is_active'] ?? 1) === 1) : ?>
                                                <span class="badge rounded-pill" style="background-color: #DEF7EC; color: #03543F; font-weight: 500; font-size: 12px; padding: 4px 12px;">Aktif</span>
                                            <?php else : ?>
                                                <span class="badge rounded-pill" style="background-color: #FDE8E8; color: #9B1C1C; font-weight: 500; font-size: 12px; padding: 4px 12px;">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">
                                        Belum ada data pengguna di OPD ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom 2: Bidang di OPD Anda -->
    <div class="col-12 col-lg-6">
        <div class="card border h-100 shadow-none opd-dashboard-card" style="border-radius: 14px; border-color: #E5E7EB; background-color: #FFFFFF; overflow: hidden;">
            <div class="card-body p-0 d-flex flex-column">
                <!-- Header Card -->
                <div class="p-3 px-4 d-flex align-items-center justify-content-between border-bottom" style="border-color: #F1F5F9 !important;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-building text-primary fs-5"></i>
                        <span class="fw-bold text-dark" style="font-size: 15px;">Bidang di OPD Anda</span>
                    </div>
                    <a href="<?= base_url('/bidang') ?>" class="text-primary text-decoration-none fw-semibold small d-flex align-items-center gap-1">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <!-- Table Bidang -->
                <div class="table-responsive flex-grow-1">
                    <table class="table align-middle mb-0" style="font-size: 13.5px;">
                        <thead style="border-bottom: 1px solid #E5E7EB; background-color: #F8FAFC;">
                            <tr>
                                <th class="text-secondary fw-semibold ps-4 py-3" style="width: 50px;">No</th>
                                <th class="text-secondary fw-semibold py-3">Nama Bidang</th>
                                <th class="text-secondary fw-semibold pe-4 py-3 text-center" style="width: 140px;">Jumlah Arsip</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($bidang_opd)) : ?>
                                <?php $no = 1; foreach ($bidang_opd as $b) : ?>
                                    <tr style="border-bottom: 1px solid #F1F5F9;">
                                        <td class="ps-4 text-muted"><?= $no++ ?></td>
                                        <td class="fw-semibold text-dark"><?= esc($b['nama_bidang']) ?></td>
                                        <td class="pe-4 text-center text-secondary"><?= number_format($b['total_arsip'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted small">
                                        Belum ada data bidang di OPD ini.
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

<!-- ============================================================== -->
<!-- RINGKASAN PROSES ALIH MEDIA (Kondisi Pengarsipan OPD)           -->
<!-- ============================================================== -->
<div class="card border shadow-none mb-4 opd-dashboard-card" style="border-radius: 14px; border-color: #E5E7EB; background-color: #FFFFFF; overflow: hidden;">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2" style="font-size: 16px;">
                    <i class="bi bi-pie-chart text-primary"></i>
                    <span>Ringkasan Proses Alih Media</span>
                </h5>
                <p class="text-secondary small mb-0">Kondisi dan progres penyelesaian pengarsipan di lingkungan OPD Anda.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill bg-light text-dark border px-2.5 py-1 fw-semibold" style="font-size: 12px;">
                    Total: <?= number_format($ringkasan_alih_media['total_arsip'] ?? 0, 0, ',', '.') ?> Arsip
                </span>
            </div>
        </div>

        <!-- Progress Bar Penyelesaian OPD -->
        <div class="opd-progress-wrapper mb-3 p-3 bg-light rounded-3 border">
            <div class="opd-progress-text mb-1 d-flex justify-content-between align-items-center">
                <span class="fw-semibold text-dark small">Progres Penyelesaian Pengarsipan:</span>
                <span class="fw-bold text-primary small">
                    <?= $ringkasan_alih_media['persentase'] ?? 0 ?>% (<?= number_format($ringkasan_alih_media['selesai'] ?? 0, 0, ',', '.') ?> dari <?= number_format($ringkasan_alih_media['total_arsip'] ?? 0, 0, ',', '.') ?> Selesai)
                </span>
            </div>
            <div class="opd-progress-track" style="height: 9px;">
                <div class="opd-progress-fill <?= ($ringkasan_alih_media['persentase'] ?? 0) >= 100 ? 'fill-completed' : (($ringkasan_alih_media['persentase'] ?? 0) == 0 ? 'fill-empty' : '') ?>" style="width: <?= $ringkasan_alih_media['persentase'] ?? 0 ?>%;"></div>
            </div>
        </div>

        <!-- 4 Card Indikator Status Alih Media -->
        <div class="opd-summary-box">
            <!-- Indikator 1: Selesai Diarsipkan -->
            <div class="opd-summary-card card-selesai">
                <div class="opd-summary-icon-box">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="opd-summary-content">
                    <span class="opd-summary-label">Selesai Diarsipkan</span>
                    <span class="opd-summary-value text-success"><?= number_format($ringkasan_alih_media['selesai'] ?? 0, 0, ',', '.') ?></span>
                </div>
            </div>

            <!-- Indikator 2: Sedang Diproses -->
            <div class="opd-summary-card card-proses">
                <div class="opd-summary-icon-box">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div class="opd-summary-content">
                    <span class="opd-summary-label">Sedang Diproses</span>
                    <span class="opd-summary-value text-primary"><?= number_format($ringkasan_alih_media['proses'] ?? 0, 0, ',', '.') ?></span>
                </div>
            </div>

            <!-- Indikator 3: Menunggu Verifikasi -->
            <div class="opd-summary-card card-menunggu">
                <div class="opd-summary-icon-box">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="opd-summary-content">
                    <span class="opd-summary-label">Menunggu Verifikasi</span>
                    <span class="opd-summary-value text-warning" style="color: #D97706 !important;"><?= number_format($ringkasan_alih_media['menunggu'] ?? 0, 0, ',', '.') ?></span>
                </div>
            </div>

            <!-- Indikator 4: Belum Selesai (Outstanding) -->
            <div class="opd-summary-card card-belum">
                <div class="opd-summary-icon-box">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <div class="opd-summary-content">
                    <span class="opd-summary-label">Belum Selesai</span>
                    <span class="opd-summary-value text-dark"><?= number_format($ringkasan_alih_media['belum_selesai'] ?? 0, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================================= -->
<!-- URUTAN 3: MONITORING WORKFLOW PENGARSIPAN (LEBAR PENUH MENGIKUTI CONTAINER DASHBOARD)     -->
<!-- ========================================================================================= -->
<div class="card border shadow-none mb-4 opd-dashboard-card" id="monitoringWorkflowOpd" style="border-radius: 14px; border-color: #E5E7EB; background-color: #FFFFFF; overflow: hidden;">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2" style="font-size: 16px;">
                    <i class="bi bi-diagram-3 text-primary"></i>
                    <span>Monitoring Workflow Pengarsipan</span>
                    <?php if (!empty($workflow_monitoring_list)) : ?>
                        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 fw-bold" style="font-size: 11px;">
                            <?= count($workflow_monitoring_list) ?> Dokumen Workflow
                        </span>
                    <?php endif; ?>
                </h5>
                <p class="text-secondary small mb-0">Pantau perkembangan Surat Perintah Tugas (SPT) dan alur alih media dokumen di lingkungan OPD Anda.</p>
            </div>
        </div>

        <!-- Toolbar Filter Workflow OPD -->
        <div class="workflow-toolbar mb-3 p-3 bg-light rounded-3 border">
            <div class="workflow-search-box flex-grow-1">
                <i class="bi bi-search"></i>
                <input type="text" id="workflowOpdSearchInput" class="form-control" placeholder="Cari nomor SPT atau nama petugas...">
            </div>

            <div class="filter-dropdown-wrapper">
                <select id="workflowOpdTahapFilter" class="dashboard-select" aria-label="Filter Tahapan">
                    <option value="all">Semua Tahapan</option>
                    <option value="1">Tahap 1: Penerbitan SPT</option>
                    <option value="2">Tahap 2: Penugasan Arsiparis</option>
                    <option value="3">Tahap 3: Proses Alih Media</option>
                    <option value="4">Tahap 4: Verifikasi Kabid</option>
                    <option value="5">Tahap 5: Persetujuan Pimpinan</option>
                </select>
            </div>

            <div class="filter-dropdown-wrapper">
                <select id="workflowOpdStatusFilter" class="dashboard-select" aria-label="Filter Status">
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

            <button type="button" id="btnResetOpdWorkflowFilter" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" style="height: 34px; font-size: 12px; border-radius: 8px;">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </div>

        <!-- Tabel Workflow Monitoring OPD (Full Width Container) -->
        <div class="table-responsive">
            <table class="table table-workflow" id="workflowOpdTable">
                <thead>
                    <tr>
                        <th class="col-wf-nomor">Nomor SPT</th>
                        <th class="col-wf-tgl text-center">Tanggal SPT</th>
                        <th class="col-wf-arsip text-center">Jumlah Arsip</th>
                        <th class="col-wf-tahap">Tahapan Alur Saat Ini</th>
                        <th class="col-wf-petugas">Petugas / Role Penanganan</th>
                        <th class="col-wf-status text-center">Status Proses</th>
                        <th class="col-wf-update text-center">Pembaruan Terakhir</th>
                        <th class="col-wf-aksi text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="workflowOpdTableBody">
                    <?php if (!empty($workflow_monitoring_list)) : ?>
                        <?php foreach ($workflow_monitoring_list as $wf) : ?>
                            <tr data-tahap="<?= esc($wf['tahap_num']) ?>" 
                                data-status="<?= esc($wf['status_proses']) ?>"
                                data-search="<?= strtolower(esc($wf['nomor_spt'] . ' ' . $wf['petugas'] . ' ' . $wf['status_proses'])) ?>">
                                <td class="col-wf-nomor">
                                    <div class="wf-spt-box">
                                        <i class="bi bi-file-earmark-text wf-spt-icon"></i>
                                        <span class="wf-spt-text"><?= esc($wf['nomor_spt']) ?></span>
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
                                    <a href="<?= base_url('/spt/detail/' . $wf['id_spt']) ?>" class="btn-wf-detail" title="Lihat Detail SPT &amp; Workflow">
                                        <i class="bi bi-eye"></i>
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr id="emptyOpdWorkflowRow">
                            <td colspan="8" class="text-center text-muted py-4">Belum ada dokumen alih media atau SPT yang tercatat di OPD Anda.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div id="workflowOpdNoMatch" class="p-4 text-center text-muted d-none">
            <i class="bi bi-search fs-2 d-block mb-1 text-secondary opacity-50"></i>
            <span>Tidak ada dokumen yang sesuai dengan filter pencarian.</span>
        </div>
    </div>
</div>

<!-- ========================================================================================= -->
<!-- URUTAN 4: DAFTAR ARSIP TERBARU & INFORMASI SPT TERKAIT                                   -->
<!-- ========================================================================================= -->
<div class="card border shadow-none mb-4 opd-dashboard-card" style="border-radius: 14px; border-color: #E5E7EB; background-color: #FFFFFF; overflow: hidden;">
    <div class="card-body p-0">
        <!-- Header Card -->
        <div class="p-3 px-4 d-flex align-items-center justify-content-between border-bottom flex-wrap gap-2" style="border-color: #F1F5F9 !important;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-primary fs-5"></i>
                <div>
                    <span class="fw-bold text-dark" style="font-size: 15px;">Daftar Arsip Terbaru</span>
                    <span class="text-muted small ms-2 d-none d-md-inline">&bull; 6 arsip terakhir di OPD Anda</span>
                </div>
            </div>
            <a href="<?= base_url('/arsip') ?>" class="text-primary text-decoration-none fw-semibold small d-flex align-items-center gap-1">
                Lihat Semua Arsip <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 13px;">
                <thead style="border-bottom: 1px solid #E5E7EB; background-color: #F8FAFC;">
                    <tr>
                        <th class="text-secondary fw-semibold ps-4 py-3" style="width: 50px;">No</th>
                        <th class="text-secondary fw-semibold py-3" style="width: 170px;">Nomor Arsip</th>
                        <th class="text-secondary fw-semibold py-3">Nama Arsip</th>
                        <th class="text-secondary fw-semibold py-3 text-center" style="width: 170px;">SPT Terkait</th>
                        <th class="text-secondary fw-semibold py-3 text-center" style="width: 130px;">Tanggal</th>
                        <th class="text-secondary fw-semibold py-3 text-center" style="width: 140px;">Status</th>
                        <th class="text-secondary fw-semibold pe-4 py-3 text-center" style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_arsip_opd)) : ?>
                        <?php $no = 1; foreach ($recent_arsip_opd as $a) : ?>
                            <tr style="border-bottom: 1px solid #F1F5F9;">
                                <td class="ps-4 text-muted"><?= $no++ ?></td>
                                <td class="fw-semibold">
                                    <a href="<?= base_url('/arsip/detail/' . $a['id_arsip']) ?>" class="text-primary text-decoration-none">
                                        <?= esc($a['nomor_arsip']) ?>
                                    </a>
                                </td>
                                <td class="text-dark">
                                    <span class="fw-medium"><?= esc($a['nama_arsip']) ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($a['nomor_spt'])) : ?>
                                        <a href="<?= base_url('/spt/detail/' . $a['id_spt']) ?>" class="badge rounded-pill bg-light text-primary border text-decoration-none px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1" title="Lihat Surat Tugas">
                                            <i class="bi bi-file-text"></i>
                                            <?= esc($a['nomor_spt']) ?>
                                        </a>
                                    <?php else : ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center text-secondary small"><?= date('d M Y', strtotime($a['created_at'])) ?></td>
                                <td class="text-center">
                                    <?php 
                                    $rawStatus = $a['status_verifikasi'] ?? 'Draft';
                                    $statusLower = strtolower($rawStatus);
                                    if (in_array($statusLower, ['selesai', 'terverifikasi', 'disetujui', 'verified'])) : ?>
                                        <span class="wf-status-badge badge-status-green">
                                            <span class="wf-status-dot"></span>
                                            <span>Terverifikasi</span>
                                        </span>
                                    <?php elseif (in_array($statusLower, ['menunggu', 'menunggu_verifikasi', 'pending', 'belum_diproses'])) : ?>
                                        <span class="wf-status-badge badge-status-amber">
                                            <span class="wf-status-dot"></span>
                                            <span>Menunggu</span>
                                        </span>
                                    <?php elseif (in_array($statusLower, ['proses', 'dalam_proses', 'sedang diproses'])) : ?>
                                        <span class="wf-status-badge badge-status-blue">
                                            <span class="wf-status-dot"></span>
                                            <span>Diproses</span>
                                        </span>
                                    <?php else : ?>
                                        <span class="wf-status-badge badge-status-purple">
                                            <span class="wf-status-dot"></span>
                                            <span><?= esc(ucfirst($statusLower)) ?></span>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-center">
                                    <a href="<?= base_url('/arsip/detail/' . $a['id_arsip']) ?>" class="btn-wf-detail" title="Detail Arsip">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <!-- Clean Empty State sesuai Desain Acuan -->
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; border: 1px solid #E2E8F0; background-color: #F8FAFC;">
                                        <i class="bi bi-file-earmark fs-3" style="color: #94A3B8;"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 16px;">Belum ada arsip</h6>
                                    <p class="text-muted small mb-0">Saat ini belum ada arsip yang tercatat di lingkungan OPD Anda.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JavaScript Interaktif untuk Filter Workflow OPD -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const wfOpdSearch = document.getElementById('workflowOpdSearchInput');
    const wfOpdTahap = document.getElementById('workflowOpdTahapFilter');
    const wfOpdStatus = document.getElementById('workflowOpdStatusFilter');
    const wfOpdReset = document.getElementById('btnResetOpdWorkflowFilter');
    const wfOpdRows = document.querySelectorAll('#workflowOpdTableBody tr');
    const wfOpdNoMatch = document.getElementById('workflowOpdNoMatch');

    function applyOpdWorkflowFilters() {
        const searchVal = (wfOpdSearch ? wfOpdSearch.value : '').toLowerCase().trim();
        const tahapVal = wfOpdTahap ? wfOpdTahap.value : 'all';
        const statusVal = wfOpdStatus ? wfOpdStatus.value : 'all';

        let visibleCount = 0;

        wfOpdRows.forEach(row => {
            if (row.id === 'emptyOpdWorkflowRow') return;

            const rowSearch = (row.getAttribute('data-search') || '').toLowerCase();
            const rowTahap = row.getAttribute('data-tahap') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchSearch = !searchVal || rowSearch.includes(searchVal);
            const matchTahap = tahapVal === 'all' || rowTahap === tahapVal;
            const matchStatus = statusVal === 'all' || rowStatus === statusVal;

            if (matchSearch && matchTahap && matchStatus) {
                row.classList.remove('d-none');
                visibleCount++;
            } else {
                row.classList.add('d-none');
            }
        });

        if (wfOpdNoMatch) {
            if (visibleCount === 0 && wfOpdRows.length > 0 && wfOpdRows[0].id !== 'emptyOpdWorkflowRow') {
                wfOpdNoMatch.classList.remove('d-none');
            } else {
                wfOpdNoMatch.classList.add('d-none');
            }
        }
    }

    if (wfOpdSearch) wfOpdSearch.addEventListener('input', applyOpdWorkflowFilters);
    if (wfOpdTahap) wfOpdTahap.addEventListener('change', applyOpdWorkflowFilters);
    if (wfOpdStatus) wfOpdStatus.addEventListener('change', applyOpdWorkflowFilters);

    if (wfOpdReset) {
        wfOpdReset.addEventListener('click', function() {
            if (wfOpdSearch) wfOpdSearch.value = '';
            if (wfOpdTahap) wfOpdTahap.value = 'all';
            if (wfOpdStatus) wfOpdStatus.value = 'all';
            applyOpdWorkflowFilters();
        });
    }
});
</script>
