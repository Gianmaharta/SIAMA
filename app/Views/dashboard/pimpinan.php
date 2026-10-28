<!-- ============================================================== -->
<!-- DASHBOARD KHUSUS PIMPINAN / KEPALA DINAS - SIAMA                -->
<!-- ============================================================== -->

<link rel="stylesheet" href="<?= base_url('css/pimpinan-dashboard.css?v=' . (file_exists(FCPATH . 'css/pimpinan-dashboard.css') ? filemtime(FCPATH . 'css/pimpinan-dashboard.css') : time())) ?>">

<div class="pimpinan-dashboard">
    <!-- 1. HEADING & SUBHEADING -->
    <div class="mb-4">
        <h1 class="pimpinan-header-title">Selamat Datang, <?= esc($nama_lengkap ?? 'Kepala Dinas') ?>!</h1>
        <p class="pimpinan-header-desc">Ini adalah ringkasan statistik berdasarkan akses Anda sebagai Kepala Dinas<?= !empty($nama_opd) ? ' - ' . esc($nama_opd) : '' ?></p>
    </div>

    <!-- 2. SUMMARY CARDS (ROW 1) -->
    <div class="row g-4 mb-4">
        <!-- Card 1: Total SPT Diterbitkan -->
        <div class="col-12 col-md-6">
            <div class="card pimpinan-stat-card h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="pimpinan-stat-icon-blue">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <div class="pimpinan-stat-label">Total SPT Diterbitkan</div>
                        <div class="pimpinan-stat-value">
                            <?= number_format($stats['spt_diterbitkan'] ?? 0, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Berita Acara Menanti TTD Pimpinan -->
        <div class="col-12 col-md-6">
            <div class="card pimpinan-stat-card h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="pimpinan-stat-icon-purple">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <div>
                        <div class="pimpinan-stat-label">Berita Acara Menanti TTD Pimpinan</div>
                        <div class="pimpinan-stat-value">
                            <?= number_format($stats['ba_menanti_pimpinan'] ?? 0, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. SECTION DATA: CARD KIRI (SPT TERBARU) & CARD KANAN (BERITA ACARA MENANTI TTD) -->
    <div class="row g-4 mb-4">
        <!-- Card Kiri: SPT Terbaru -->
        <div class="col-12 col-lg-6">
            <div class="card pimpinan-table-card h-100">
                <div class="card-body p-0 d-flex flex-column">
                    <!-- Header Card -->
                    <div class="pimpinan-table-header">
                        <div class="pimpinan-table-title-group">
                            <div class="pimpinan-table-icon-blue">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <h2 class="pimpinan-table-title">SPT Terbaru</h2>
                        </div>
                        <a href="<?= base_url('/spt') ?>" class="pimpinan-link-all">
                            Lihat Semua <i class="bi bi-chevron-right" style="font-size: 11px;"></i>
                        </a>
                    </div>

                    <!-- Tabel SPT Terbaru -->
                    <div class="table-responsive flex-grow-1">
                        <table class="table align-middle pimpinan-table mb-0">
                            <colgroup>
                                <col style="width: 38px;">
                                <col style="width: 45%;">
                                <col style="width: 90px;">
                                <col style="width: auto;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomor SPT</th>
                                    <th>Tanggal</th>
                                    <th>Perihal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recent_spt)) : ?>
                                    <?php $no = 1; foreach ($recent_spt as $row) : ?>
                                        <tr>
                                            <td class="text-muted" style="color: #94A3B8 !important;"><?= $no++ ?></td>
                                            <td>
                                                <a href="<?= base_url('/spt') ?>" class="pimpinan-nomor-spt" title="<?= esc($row['nomor_spt']) ?>">
                                                    <?= esc($row['nomor_spt']) ?>
                                                </a>
                                            </td>
                                            <td class="text-secondary text-nowrap" style="color: #64748B !important;">
                                                <?= esc(date('Y-m-d', strtotime($row['tanggal_spt'] ?? date('Y-m-d')))) ?>
                                            </td>
                                            <td class="text-secondary text-truncate" style="color: #64748B !important;" title="<?= esc($row['perihal']) ?>">
                                                <?= esc($row['perihal']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">
                                            Belum ada SPT yang diterbitkan di OPD ini.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Kanan: Berita Acara Menanti TTD -->
        <div class="col-12 col-lg-6">
            <div class="card pimpinan-table-card h-100">
                <div class="card-body p-0 d-flex flex-column">
                    <!-- Header Card -->
                    <div class="pimpinan-table-header">
                        <div class="pimpinan-table-title-group">
                            <div class="pimpinan-table-icon-purple">
                                <i class="bi bi-clock"></i>
                            </div>
                            <h2 class="pimpinan-table-title">Berita Acara Menanti TTD</h2>
                        </div>
                        <a href="<?= base_url('/berita-acara') ?>" class="pimpinan-link-all">
                            Lihat Semua <i class="bi bi-chevron-right" style="font-size: 11px;"></i>
                        </a>
                    </div>

                    <!-- Tabel Berita Acara -->
                    <div class="table-responsive flex-grow-1">
                        <table class="table align-middle pimpinan-table mb-0">
                            <colgroup>
                                <col style="width: 38px;">
                                <col style="width: 36%;">
                                <col style="width: 90px;">
                                <col style="width: auto;">
                                <col style="width: 100px;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomor BA</th>
                                    <th>Tanggal</th>
                                    <th>Perihal</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recent_ba)) : ?>
                                    <?php $noBa = 1; foreach ($recent_ba as $b) : ?>
                                        <tr>
                                            <td class="text-muted" style="color: #94A3B8 !important;"><?= $noBa++ ?></td>
                                            <td>
                                                <a href="<?= base_url('/berita-acara') ?>" class="pimpinan-nomor-ba" title="<?= esc($b['nomor_ba']) ?>">
                                                    <?= esc($b['nomor_ba']) ?>
                                                </a>
                                            </td>
                                            <td class="text-secondary text-nowrap" style="color: #64748B !important;">
                                                <?php
                                                $tglBa = $b['tanggal_ba'] ?? date('Y-m-d');
                                                echo esc(date('Y-m-d', strtotime($tglBa)));
                                                ?>
                                            </td>
                                            <td class="text-secondary text-truncate" style="color: #64748B !important;" title="<?= esc(!empty($b['perihal']) ? $b['perihal'] : '-') ?>">
                                                <?= esc(!empty($b['perihal']) ? $b['perihal'] : '-') ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge-draf-kabid">
                                                    <span class="dot-orange"></span>
                                                    <span><?= esc($b['status_persetujuan'] ?? 'Draf_Pimpinan') ?></span>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">
                                            Belum ada Berita Acara yang menanti tanda tangan Anda.
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

