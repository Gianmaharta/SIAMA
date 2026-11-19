<!-- ============================================================== -->
<!-- DASHBOARD KHUSUS ARSIPARIS - SIAMA                             -->
<!-- ============================================================== -->

<link rel="stylesheet" href="<?= base_url('css/arsiparis-dashboard.css?v=' . (file_exists(FCPATH . 'css/arsiparis-dashboard.css') ? filemtime(FCPATH . 'css/arsiparis-dashboard.css') : time())) ?>">

<?php
$sptBelumDikerjakan = $spt_belum_dikerjakan ?? [];
$monitoringArsip    = $monitoring_arsip_list ?? [];
$baList             = $ba_arsiparis_list ?? [];

$statusData = $status_arsip_arsiparis ?? [
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
?>

<div class="arsiparis-dashboard">
    <!-- ============================================================== -->
    <!-- HEADING & SAMBUTAN DASHBOARD                                   -->
    <!-- ============================================================== -->
    <div class="mb-4">
        <h1 class="arsiparis-header-title">Selamat Datang, <?= esc($nama_lengkap ?? 'Arsiparis') ?>!</h1>
        <p class="arsiparis-header-desc">
            Ini adalah ringkasan pekerjaan, penugasan SPT, dan perkembangan proses alih media arsip Anda<?= !empty($nama_opd) ? ' di lingkungan ' . esc($nama_opd) : '' ?>.
        </p>
    </div>

    <!-- ============================================================== -->
    <!-- 1. PEKERJAAN BELUM DIKERJAKAN (PRIORITAS UTAMA DI PALING ATAS)  -->
    <!-- ============================================================== -->
    <div class="arsiparis-priority-card <?= !empty($sptBelumDikerjakan) ? 'has-tasks' : '' ?> mb-4">
        <div class="arsiparis-card-header">
            <div>
                <div class="arsiparis-card-title-group">
                    <i class="bi bi-exclamation-diamond-fill text-warning fs-5"></i>
                    <h2 class="arsiparis-card-title">Pekerjaan Belum Dikerjakan</h2>
                </div>
                <p class="arsiparis-card-subtitle">
                    Daftar Surat Perintah Tugas (SPT) aktif yang ditugaskan kepada Anda namun proses alih medianya belum dimulai.
                </p>
            </div>
            <div>
                <?php if (!empty($sptBelumDikerjakan)) : ?>
                    <span class="arsiparis-badge-priority">
                        <span class="pulse-dot"></span>
                        <?= count($sptBelumDikerjakan) ?> Tugas Perlu Tindak Lanjut
                    </span>
                <?php else : ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-medium" style="font-size: 11.5px;">
                        <i class="bi bi-check2-circle me-1"></i> Semua Tugas Sudah Dimulai
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table arsiparis-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 170px;">Nomor SPT</th>
                        <th style="width: 110px;">Tanggal SPT</th>
                        <th>Perihal / Uraian Tugas</th>
                        <th style="width: 140px;">Tanggal Penugasan</th>
                        <th style="width: 110px;" class="text-center">Jumlah Arsip</th>
                        <th style="width: 130px;" class="text-center">Status Pekerjaan</th>
                        <th style="width: 220px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sptBelumDikerjakan)) : ?>
                        <?php foreach ($sptBelumDikerjakan as $spt) : ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($spt['nomor_spt']) ?></div>
                                    <?php if (!empty($spt['file_spt'])) : ?>
                                        <a href="<?= base_url('uploads/spt/' . $spt['file_spt']) ?>" target="_blank" class="btn-action-pdf mt-1" title="Lihat Dokumen SPT (PDF)">
                                            <i class="bi bi-file-earmark-pdf-fill"></i> Dokumen SPT
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td class="text-secondary">
                                    <?= !empty($spt['tanggal_spt']) ? date('d/m/Y', strtotime($spt['tanggal_spt'])) : '-' ?>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium mb-1" style="line-height: 1.4;">
                                        <?= esc($spt['perihal'] ?? 'Tugas Alih Media Arsip') ?>
                                    </div>
                                    <?php if (!empty($spt['tanggal_mulai']) || !empty($spt['tanggal_selesai'])) : ?>
                                        <div class="text-muted small" style="font-size: 11px;">
                                            Periode: <?= esc(date('d/m/Y', strtotime($spt['tanggal_mulai'] ?? 'now'))) ?> s/d <?= esc(date('d/m/Y', strtotime($spt['tanggal_selesai'] ?? 'now'))) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-secondary">
                                    <?php if (!empty($spt['tanggal_penugasan'])) : ?>
                                        <div class="fw-medium text-dark"><?= date('d/m/Y', strtotime($spt['tanggal_penugasan'])) ?></div>
                                        <div class="text-muted small" style="font-size: 11px;"><?= date('H:i', strtotime($spt['tanggal_penugasan'])) ?> WITA</div>
                                    <?php else : ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill" style="font-size: 11.5px;">
                                        <?= (int)($spt['jumlah_arsip'] ?? 0) ?> Arsip
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-status badge-status-amber">
                                        <span class="badge-status-dot"></span>
                                        <span>Belum Dimulai</span>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-2 flex-wrap justify-content-center table-action-group" style="gap: 8px; row-gap: 8px;">
                                        <a href="<?= base_url('/arsip/create?id_spt=' . $spt['id_spt']) ?>" class="btn-action-start" title="Mulai unggah berkas alih media untuk SPT ini">
                                            <i class="bi bi-folder-plus"></i> Mulai Alih Media
                                        </a>
                                        <a href="<?= base_url('/spt/detail/' . $spt['id_spt']) ?>" class="btn-action-detail" title="Lihat Informasi Lengkap SPT">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="p-0 border-0">
                                <div class="empty-state-box">
                                    <div class="empty-state-icon" style="background-color: #ECFDF5; color: #10B981;">
                                        <i class="bi bi-shield-check"></i>
                                    </div>
                                    <div class="empty-state-title">Tidak ada pekerjaan tertunda</div>
                                    <p class="empty-state-desc">
                                        Semua penugasan SPT yang diterbitkan untuk Anda telah dimulai atau diselesaikan.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 2. RINGKASAN PEKERJAAN (4 KARTU STATISTIK BEBAN KERJA)          -->
    <!-- ============================================================== -->
    <div class="mb-4">
        <div class="arsiparis-stat-grid">
            <!-- Card 1: Total Penugasan SPT -->
            <div class="arsiparis-stat-card">
                <div class="arsiparis-stat-icon icon-blue">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div>
                    <div class="arsiparis-stat-label">Total Penugasan SPT</div>
                    <div class="arsiparis-stat-value">
                        <?= number_format($stats['penugasan_spt'] ?? 0, 0, ',', '.') ?>
                    </div>
                    <div class="arsiparis-stat-sub">Surat tugas untuk Anda</div>
                </div>
            </div>

            <!-- Card 2: Pekerjaan Belum Dimulai -->
            <div class="arsiparis-stat-card">
                <div class="arsiparis-stat-icon icon-amber">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <div>
                    <div class="arsiparis-stat-label">Pekerjaan Belum Dimulai</div>
                    <div class="arsiparis-stat-value text-warning">
                        <?= number_format($stats['pekerjaan_belum_dimulai'] ?? 0, 0, ',', '.') ?>
                    </div>
                    <div class="arsiparis-stat-sub">SPT menanti alih media</div>
                </div>
            </div>

            <!-- Card 3: Arsip Sedang Diproses -->
            <div class="arsiparis-stat-card">
                <div class="arsiparis-stat-icon icon-indigo">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div>
                    <div class="arsiparis-stat-label">Arsip Sedang Diproses</div>
                    <div class="arsiparis-stat-value" style="color: #4F46E5;">
                        <?= number_format($stats['arsip_sedang_diproses'] ?? 0, 0, ',', '.') ?>
                    </div>
                    <div class="arsiparis-stat-sub">Draf &amp; alih media aktif</div>
                </div>
            </div>

            <!-- Card 4: Arsip Menunggu Verifikasi -->
            <div class="arsiparis-stat-card">
                <div class="arsiparis-stat-icon icon-emerald">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="arsiparis-stat-label">Arsip Menunggu Verifikasi</div>
                    <div class="arsiparis-stat-value text-success">
                        <?= number_format($stats['arsip_menunggu_verifikasi'] ?? 0, 0, ',', '.') ?>
                    </div>
                    <div class="arsiparis-stat-sub">Menanti telaah Kabid</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 3. MONITORING PROSES ALIH MEDIA (ARSIP BERJALAN)               -->
    <!-- ============================================================== -->
    <div class="arsiparis-table-wrapper mb-4">
        <div class="arsiparis-card-header">
            <div>
                <div class="arsiparis-card-title-group">
                    <i class="bi bi-diagram-3-fill text-primary fs-5"></i>
                    <h2 class="arsiparis-card-title">Monitoring Proses Alih Media</h2>
                </div>
                <p class="arsiparis-card-subtitle">
                    Perkembangan dokumen alih media yang sudah dimulai dan sedang ditangani.
                </p>
            </div>
            <a href="<?= base_url('/arsip') ?>" class="footer-report-link">
                Lihat Semua Arsip <i class="bi bi-chevron-right" style="font-size: 11px;"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table arsiparis-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 170px;">Nomor Arsip</th>
                        <th>Nama / Judul Arsip</th>
                        <th style="width: 180px;" class="text-center">Nomor SPT Terkait</th>
                        <th style="width: 190px;">Tahapan Proses Saat Ini</th>
                        <th style="width: 130px;" class="text-center">Status Pekerjaan</th>
                        <th style="width: 130px;" class="text-center">Pembaruan Terakhir</th>
                        <th style="width: 110px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($monitoringArsip)) : ?>
                        <?php foreach ($monitoringArsip as $rowArsip) : ?>
                            <tr>
                                <td>
                                    <a href="<?= base_url('/arsip/detail/' . $rowArsip['id_arsip']) ?>" class="fw-semibold text-dark text-decoration-none">
                                        <?= esc($rowArsip['nomor_arsip']) ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><?= esc($rowArsip['nama_arsip']) ?></div>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($rowArsip['nomor_spt'])) : ?>
                                        <span class="text-dark small fw-medium"><?= esc($rowArsip['nomor_spt']) ?></span>
                                    <?php else : ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="text-dark small fw-medium">
                                        <i class="bi bi-arrow-right-short text-primary"></i> <?= esc($rowArsip['tahapan_label']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $stClass = 'badge-status-gray';
                                    $stLabel = $rowArsip['status_verifikasi'] ?? 'Draft';
                                    if (in_array($stLabel, ['Selesai', 'Disetujui', 'Terverifikasi'])) {
                                        $stClass = 'badge-status-green';
                                    } elseif (in_array($stLabel, ['Menunggu', 'Belum_Diproses'])) {
                                        $stClass = 'badge-status-amber';
                                    } elseif (in_array($stLabel, ['Proses', 'Dalam_Proses'])) {
                                        $stClass = 'badge-status-blue';
                                    } elseif (in_array($stLabel, ['Ditolak', 'Revisi', 'Gagal'])) {
                                        $stClass = 'badge-status-red';
                                    }
                                    ?>
                                    <span class="badge-status <?= $stClass ?>">
                                        <span class="badge-status-dot"></span>
                                        <span><?= esc($stLabel) ?></span>
                                    </span>
                                </td>
                                <td class="text-center text-secondary small">
                                    <?php
                                    $tglUpdate = $rowArsip['updated_at'] ?: $rowArsip['created_at'];
                                    echo !empty($tglUpdate) ? date('d/m/Y H:i', strtotime($tglUpdate)) : '-';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-2 flex-wrap justify-content-center" style="gap: 8px; row-gap: 8px;">
                                        <a href="<?= base_url('/arsip/detail/' . $rowArsip['id_arsip']) ?>" class="btn-action-detail" title="Lihat Rincian Arsip">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        <?php if (in_array($stLabel, ['Draft', 'Revisi', 'Ditolak'])) : ?>
                                            <a href="<?= base_url('/arsip/edit/' . $rowArsip['id_arsip']) ?>" class="btn-action-edit" title="Edit / Perbaiki Berkas">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="p-0 border-0">
                                <div class="empty-state-box">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <div class="empty-state-title">Belum ada pekerjaan alih media</div>
                                    <p class="empty-state-desc">Belum ada berkas arsip yang sedang diproses atau diunggah.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 4 & 5. STATUS VERIFIKASI ARSIP & BERITA ACARA (2 KOLOM)        -->
    <!-- ============================================================== -->
    <div class="row g-4 mb-4">
        <!-- Kolom Kiri: Status Verifikasi Arsip (Donut Chart & Legend) -->
        <div class="col-12 col-lg-5">
            <div class="arsiparis-panel-card">
                <div class="arsiparis-card-header p-0 pb-3 border-bottom mb-3">
                    <div>
                        <h2 class="arsiparis-card-title">Status Verifikasi Arsip</h2>
                        <p class="arsiparis-card-subtitle">Rasio hasil telaah berkas arsip yang Anda tangani</p>
                    </div>
                    <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill" style="font-size: 11.5px;">
                        Total: <?= number_format($statusData['total'] ?? 0, 0, ',', '.') ?> Berkas
                    </span>
                </div>

                <div class="row align-items-center g-3 my-auto py-2">
                    <div class="col-12 col-sm-5 text-center">
                        <div class="donut-chart-container">
                            <canvas id="arsiparisDonutChart" width="160" height="160"></canvas>
                            <div class="donut-center-text">
                                <div class="donut-center-number"><?= (int)($statusData['total'] ?? 0) ?></div>
                                <div class="donut-center-label">Total Arsip</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-7">
                        <div class="status-pill-list">
                            <!-- Status 1: Selesai Diverifikasi -->
                            <div class="status-pill-item green">
                                <div class="status-item-label">
                                    <span class="status-dot green"></span>
                                    <span>Selesai Diverifikasi</span>
                                </div>
                                <div class="status-item-meta">
                                    <div class="status-item-count"><?= (int)($statusData['selesai'] ?? 0) ?></div>
                                    <div class="status-item-pct text-muted small"><?= $statusData['selesai_pct'] ?>%</div>
                                </div>
                            </div>

                            <!-- Status 2: Dalam Proses Verifikasi atau Telaah -->
                            <div class="status-pill-item amber">
                                <div class="status-item-label">
                                    <span class="status-dot amber"></span>
                                    <span>Dalam Proses / Telaah</span>
                                </div>
                                <div class="status-item-meta">
                                    <div class="status-item-count"><?= (int)($statusData['proses'] ?? 0) ?></div>
                                    <div class="status-item-pct text-muted small"><?= $statusData['proses_pct'] ?>%</div>
                                </div>
                            </div>

                            <!-- Status 3: Ditolak atau Perlu Revisi -->
                            <div class="status-pill-item gray">
                                <div class="status-item-label">
                                    <span class="status-dot gray"></span>
                                    <span>Ditolak / Revisi</span>
                                </div>
                                <div class="status-item-meta">
                                    <div class="status-item-count"><?= (int)($statusData['gagal'] ?? 0) ?></div>
                                    <div class="status-item-pct text-muted small"><?= $statusData['gagal_pct'] ?>%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Berita Acara Pekerjaan Arsiparis -->
        <div class="col-12 col-lg-7">
            <div class="arsiparis-panel-card p-0 overflow-hidden">
                <div class="arsiparis-card-header">
                    <div>
                        <h2 class="arsiparis-card-title">Berita Acara</h2>
                        <p class="arsiparis-card-subtitle">Daftar Berita Acara yang berkaitan dengan pekerjaan Anda</p>
                    </div>
                    <a href="<?= base_url('/berita-acara') ?>" class="footer-report-link">
                        Lihat Semua <i class="bi bi-chevron-right" style="font-size: 11px;"></i>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table arsiparis-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 150px;">Nomor BA</th>
                                <th style="width: 100px;">Tanggal</th>
                                <th class="text-center">Nomor SPT Terkait</th>
                                <th style="width: 130px;" class="text-center">Status</th>
                                <th style="width: 90px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($baList)) : ?>
                                <?php foreach ($baList as $ba) : ?>
                                    <tr>
                                        <td>
                                            <a href="<?= base_url('/berita-acara/detail/' . $ba['id_berita_acara']) ?>" class="fw-semibold text-dark text-decoration-none">
                                                <?= esc($ba['nomor_ba']) ?>
                                            </a>
                                        </td>
                                        <td class="text-secondary small">
                                            <?= !empty($ba['tanggal_ba']) ? date('d/m/Y', strtotime($ba['tanggal_ba'])) : '-' ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($ba['nomor_spt'])) : ?>
                                                <span class="text-dark small fw-medium"><?= esc($ba['nomor_spt']) ?></span>
                                            <?php else : ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $stBa = $ba['status_persetujuan'] ?? 'Draft';
                                            if ($stBa === 'Selesai' || $stBa === 'Selesai_Disahkan') : ?>
                                                <span class="badge-status badge-status-green">
                                                    <span class="badge-status-dot"></span>
                                                    <span>Selesai</span>
                                                </span>
                                            <?php elseif ($stBa === 'Draf_Pimpinan' || $stBa === 'Menunggu_Pimpinan') : ?>
                                                <span class="badge-status badge-status-blue">
                                                    <span class="badge-status-dot"></span>
                                                    <span>TTD Pimpinan</span>
                                                </span>
                                            <?php elseif ($stBa === 'Draf_Kabid') : ?>
                                                <span class="badge-status badge-status-amber">
                                                    <span class="badge-status-dot"></span>
                                                    <span>Verifikasi Kabid</span>
                                                </span>
                                            <?php elseif ($stBa === 'Revisi' || $stBa === 'Ditolak') : ?>
                                                <span class="badge-status badge-status-red">
                                                    <span class="badge-status-dot"></span>
                                                    <span>Revisi</span>
                                                </span>
                                            <?php else : ?>
                                                <span class="badge-status badge-status-gray">
                                                    <span class="badge-status-dot"></span>
                                                    <span><?= esc(str_replace('_', ' ', $stBa)) ?></span>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('/berita-acara/detail/' . $ba['id_berita_acara']) ?>" class="btn-action-detail" title="Lihat Rincian Berita Acara">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" class="p-0 border-0">
                                        <div class="empty-state-box">
                                            <div class="empty-state-icon">
                                                <i class="bi bi-clipboard-x"></i>
                                            </div>
                                            <div class="empty-state-title">Tidak ada data</div>
                                            <p class="empty-state-desc">Belum ada berita acara yang berkaitan dengan pekerjaan Anda.</p>
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

<!-- ============================================================== -->
<!-- SCRIPT INISIALISASI CHART.JS DASHBOARD ARSIPARIS              -->
<!-- ============================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const donutCanvas = document.getElementById('arsiparisDonutChart');
    if (donutCanvas) {
        const selesaiCount = <?= (int)($statusData['selesai'] ?? 0) ?>;
        const prosesCount  = <?= (int)($statusData['proses'] ?? 0) ?>;
        const gagalCount   = <?= (int)($statusData['gagal'] ?? 0) ?>;
        const totalCount   = <?= (int)($statusData['total'] ?? 0) ?>;

        const hasData = totalCount > 0;
        const chartData = hasData 
            ? [selesaiCount, prosesCount, gagalCount] 
            : [0, 0, 1]; // Placeholder jika 0

        const chartColors = hasData 
            ? ['#10B981', '#F59E0B', '#EF4444'] 
            : ['#E2E8F0', '#E2E8F0', '#E2E8F0'];

        new Chart(donutCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: hasData 
                    ? ['Selesai Diverifikasi', 'Dalam Proses / Telaah', 'Ditolak / Revisi'] 
                    : ['Belum Ada Data'],
                datasets: [{
                    data: chartData,
                    backgroundColor: chartColors,
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                    hoverOffset: hasData ? 4 : 0
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: hasData,
                        callbacks: {
                            label: function (ctx) {
                                const val = ctx.raw || 0;
                                const pct = totalCount > 0 ? Math.round((val / totalCount) * 100) : 0;
                                return ' ' + ctx.label + ': ' + val + ' berkas (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
