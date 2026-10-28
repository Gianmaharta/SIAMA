<!-- ============================================================== -->
<!-- DASHBOARD KHUSUS ARSIPARIS - SIAMA                             -->
<!-- ============================================================== -->

<link rel="stylesheet" href="<?= base_url('css/arsiparis-dashboard.css?v=' . (file_exists(FCPATH . 'css/arsiparis-dashboard.css') ? filemtime(FCPATH . 'css/arsiparis-dashboard.css') : time())) ?>">

<?php
$statusData = $status_arsip_arsiparis ?? [
    'selesai'     => 0,
    'proses'      => 0,
    'gagal'       => 0,
    'total'       => 0,
    'selesai_pct' => 0,
    'proses_pct'  => 0,
    'gagal_pct'   => 0,
];

$monthlyData = $monthly_arsip_arsiparis ?? [
    'labels'        => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
    'values'        => array_fill(0, 12, 0),
    'tahun'         => date('Y'),
    'peak_text'     => '-',
    'avg_per_cycle' => 0,
];

$baList = $ba_arsiparis_list ?? [];
?>

<div class="arsiparis-dashboard">
    <!-- 1. HEADING & SUBHEADING -->
    <div class="mb-4">
        <h1 class="arsiparis-header-title">Selamat Datang, <?= esc($nama_lengkap ?? 'Budi Santoso') ?>!</h1>
        <p class="arsiparis-header-desc">Ini adalah ringkasan aktivitas Anda sebagai Arsiparis.</p>
    </div>

    <!-- 2. SUMMARY CARDS (ROW 1: 2 CARDS HORIZONTAL) -->
    <div class="row g-4 mb-4">
        <!-- Card 1: Total Arsip yang Anda Unggah -->
        <div class="col-12 col-md-6">
            <div class="card arsiparis-stat-card h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="arsiparis-stat-icon-purple">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <div class="arsiparis-stat-label">Total Arsip yang Anda Unggah</div>
                        <div class="arsiparis-stat-value">
                            <?= number_format($stats['arsip_diunggah'] ?? $statusData['total'] ?? 0, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Penugasan SPT Anda -->
        <div class="col-12 col-md-6">
            <div class="card arsiparis-stat-card h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="arsiparis-stat-icon-blue">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <div class="arsiparis-stat-label">Penugasan SPT Anda</div>
                        <div class="arsiparis-stat-value">
                            <?= number_format($stats['penugasan_spt'] ?? 0, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. MENU AKSES CEPAT (4 CARDS) -->
    <div class="mb-4">
        <h2 class="quick-access-title">Menu Akses Cepat</h2>
        <p class="quick-access-subtitle">Pilih menu untuk mengelola data sesuai dengan tugas Anda sebagai Arsiparis.</p>

        <div class="row g-3 g-md-4">
            <!-- Akses Cepat 1: Pengelolaan Arsip -->
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="<?= base_url('/arsip') ?>" class="quick-access-card">
                    <div class="quick-access-icon-box green">
                        <i class="bi bi-folder"></i>
                    </div>
                    <div class="quick-card-title">Pengelolaan Arsip</div>
                    <div class="quick-card-desc">Kelola dan unggah arsip digital</div>
                    <div class="quick-card-arrow">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                </a>
            </div>

            <!-- Akses Cepat 2: Surat Perintah Tugas (SPT) -->
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="<?= base_url('/spt') ?>" class="quick-access-card">
                    <div class="quick-access-icon-box amber">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div class="quick-card-title">Surat Perintah Tugas (SPT)</div>
                    <div class="quick-card-desc">Lihat daftar penugasan SPT Anda</div>
                    <div class="quick-card-arrow">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                </a>
            </div>

            <!-- Akses Cepat 3: Berita Acara -->
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="<?= base_url('/berita-acara') ?>" class="quick-access-card">
                    <div class="quick-access-icon-box blue">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <div class="quick-card-title">Berita Acara</div>
                    <div class="quick-card-desc">Lihat dan kelola berita acara</div>
                    <div class="quick-card-arrow">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                </a>
            </div>

            <!-- Akses Cepat 4: Notifikasi -->
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="<?= base_url('/notification') ?>" class="quick-access-card">
                    <div class="quick-access-icon-box purple">
                        <i class="bi bi-bell"></i>
                    </div>
                    <div class="quick-card-title">Notifikasi</div>
                    <div class="quick-card-desc">Lihat pemberitahuan sistem</div>
                    <div class="quick-card-arrow">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- 4. MIDDLE ROW: CARD KIRI (STATUS VERIFIKASI ARSIP) & CARD KANAN (TREN PENGUNGGAHAN ARSIP) -->
    <div class="row g-4 mb-4">
        <!-- Card Kiri: Status Verifikasi Arsip (Donut Chart) -->
        <div class="col-12 col-lg-6">
            <div class="arsiparis-panel-card">
                <!-- Header Card -->
                <div class="arsiparis-panel-header">
                    <div>
                        <h2 class="arsiparis-panel-title">Status Verifikasi Arsip</h2>
                        <p class="arsiparis-panel-subtitle">Rasio kelayakan berkas yang telah Anda unggah</p>
                    </div>
                    <span class="arsiparis-pill-badge">
                        Total: <?= number_format($statusData['total'] ?? 0, 0, ',', '.') ?> Berkas
                    </span>
                </div>

                <!-- Body Card (Donut Chart & Legend Stack) -->
                <div class="row align-items-center g-3 my-auto py-2">
                    <div class="col-12 col-sm-5 text-center">
                        <div class="donut-chart-container">
                            <canvas id="arsiparisDonutChart" width="170" height="170"></canvas>
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
                                    <div class="status-item-count"><?= (int)($statusData['selesai'] ?? 0) ?> arsip</div>
                                </div>
                            </div>

                            <!-- Status 2: Dalam Proses Telaah -->
                            <div class="status-pill-item amber">
                                <div class="status-item-label">
                                    <span class="status-dot amber"></span>
                                    <span>Dalam Proses Telaah</span>
                                </div>
                                <div class="status-item-meta">
                                    <div class="status-item-count"><?= (int)($statusData['proses'] ?? 0) ?> arsip</div>
                                </div>
                            </div>

                            <!-- Status 3: Ditolak / Revisi -->
                            <div class="status-pill-item gray">
                                <div class="status-item-label">
                                    <span class="status-dot gray"></span>
                                    <span>Ditolak / Revisi</span>
                                </div>
                                <div class="status-item-meta">
                                    <div class="status-item-count"><?= (int)($statusData['gagal'] ?? 0) ?> arsip</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Kanan: Tren Pengunggahan Arsip (Bar Chart) -->
        <div class="col-12 col-lg-6">
            <div class="arsiparis-panel-card">
                <!-- Header Card -->
                <div class="arsiparis-panel-header">
                    <div>
                        <h2 class="arsiparis-panel-title">Tren Pengunggahan Arsip <?= esc($monthlyData['tahun'] ?? date('Y')) ?></h2>
                        <p class="arsiparis-panel-subtitle">Volume alih media dokumen arsip per bulan</p>
                    </div>
                    <?php if (!empty($monthlyData['peak_text']) && $monthlyData['peak_text'] !== '-') : ?>
                        <span class="arsiparis-pill-badge">
                            <i class="bi bi-graph-up-arrow"></i> Puncak: <?= esc($monthlyData['peak_text']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Body Card (Bar Chart) -->
                <div class="my-auto py-2">
                    <div style="height: 185px; position: relative;">
                        <canvas id="arsiparisBarChart"></canvas>
                    </div>
                </div>

                <!-- Footer Card -->
                <div class="arsiparis-card-footer">
                    <span>Rata-rata produktivitas: <strong><?= esc($monthlyData['avg_per_cycle'] ?? 0) ?> berkas/siklus</strong></span>
                    <a href="<?= base_url('/arsip') ?>" class="footer-report-link">
                        Lihat Laporan Detail <i class="bi bi-chevron-right" style="font-size: 11px;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. CARD TABEL: BERITA ACARA MENANTI VERIFIKASI -->
    <div class="arsiparis-table-wrapper mb-4">
        <div class="arsiparis-table-header">
            <div>
                <h2 class="arsiparis-panel-title">Berita Acara Menanti Verifikasi</h2>
                <p class="arsiparis-panel-subtitle">Daftar berita acara alih media arsip yang sedang dalam proses verifikasi atau pengesahan</p>
            </div>
            <a href="<?= base_url('/berita-acara') ?>" class="footer-report-link">
                Lihat Semua <i class="bi bi-chevron-right" style="font-size: 11px;"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle arsiparis-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nomor BA</th>
                        <th>Tanggal</th>
                        <th>Pelaksana</th>
                        <th>Status</th>
                        <th style="width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($baList)) : ?>
                        <?php foreach ($baList as $idx => $ba) : ?>
                            <tr>
                                <td class="fw-medium text-muted"><?= $idx + 1 ?></td>
                                <td class="fw-semibold text-dark">
                                    <a href="<?= base_url('/berita-acara/detail/' . $ba['id_berita_acara']) ?>" class="text-decoration-none text-dark">
                                        <?= esc($ba['nomor_ba']) ?>
                                    </a>
                                    <?php if (!empty($ba['nomor_spt'])) : ?>
                                        <div class="text-muted small" style="font-size: 11.5px;">SPT: <?= esc($ba['nomor_spt']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= !empty($ba['tanggal_ba']) ? date('d M Y', strtotime($ba['tanggal_ba'])) : '-' ?>
                                </td>
                                <td>
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-person-circle text-secondary"></i>
                                        <?= esc($ba['nama_pelaksana'] ?? ($nama_lengkap ?? 'Arsiparis')) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $st = $ba['status_persetujuan'] ?? '';
                                    if ($st === 'Selesai' || $st === 'Selesai_Disahkan') : ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size: 11.5px;">
                                            <i class="bi bi-check-circle-fill me-1"></i>Selesai Disahkan
                                        </span>
                                    <?php elseif ($st === 'Menunggu_Pimpinan') : ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill" style="font-size: 11.5px;">
                                            <i class="bi bi-hourglass-split me-1"></i>Menunggu TTD Pimpinan
                                        </span>
                                    <?php elseif ($st === 'Draf_Kabid') : ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-pill" style="font-size: 11.5px;">
                                            <i class="bi bi-clock-history me-1"></i>Menunggu Verifikasi Kabid
                                        </span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill" style="font-size: 11.5px;">
                                            <?= esc(str_replace('_', ' ', $st ?: 'Draft')) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('/berita-acara/detail/' . $ba['id_berita_acara']) ?>" class="btn btn-sm btn-light border py-1 px-3 rounded-2 text-primary fw-medium" style="font-size: 12.5px;" title="Lihat Rincian Berita Acara">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6">
                                <div class="ba-empty-state">
                                    <div class="ba-empty-icon">
                                        <i class="bi bi-clipboard-x"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 15px;">Tidak ada data</h6>
                                    <p class="small text-secondary mb-0">Belum ada berita acara yang perlu diverifikasi.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- SCRIPT INISIALISASI CHART.JS DASHBOARD ARSIPARIS              -->
<!-- ============================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Inisialisasi Donut Chart: Status Verifikasi Arsip
    const donutCanvas = document.getElementById('arsiparisDonutChart');
    if (donutCanvas) {
        const selesaiCount = <?= (int)($statusData['selesai'] ?? 0) ?>;
        const prosesCount  = <?= (int)($statusData['proses'] ?? 0) ?>;
        const gagalCount   = <?= (int)($statusData['gagal'] ?? 0) ?>;
        const totalCount   = <?= (int)($statusData['total'] ?? 0) ?>;

        const hasData = totalCount > 0;
        const chartData = hasData 
            ? [selesaiCount, prosesCount, gagalCount] 
            : [0, 0, 1]; // Clean placeholder ring if 0 archives

        const chartColors = hasData 
            ? ['#10B981', '#F59E0B', '#E2E8F0'] 
            : ['#E2E8F0', '#E2E8F0', '#E2E8F0'];

        new Chart(donutCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Selesai Diverifikasi', 'Dalam Proses Telaah', 'Ditolak / Revisi'],
                datasets: [{
                    data: chartData,
                    backgroundColor: chartColors,
                    borderWidth: 0,
                    hoverOffset: hasData ? 3 : 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '76%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: hasData,
                        callbacks: {
                            label: function (context) {
                                const val = context.raw || 0;
                                return ` ${context.label}: ${val} arsip`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Inisialisasi Bar Chart: Tren Pengunggahan Arsip
    const barCanvas = document.getElementById('arsiparisBarChart');
    if (barCanvas) {
        const monthlyLabels = <?= json_encode($monthlyData['labels'] ?? []) ?>;
        const monthlyValues = <?= json_encode($monthlyData['values'] ?? []) ?>;

        // Cari nilai maksimum untuk menentukan warna bar puncak
        const maxVal = Math.max(...monthlyValues, 0);

        const barColors = monthlyValues.map(v => (v > 0 && v === maxVal) ? '#1E60EC' : '#93C5FD');

        new Chart(barCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Arsip Diunggah',
                    data: monthlyValues,
                    backgroundColor: barColors,
                    hoverBackgroundColor: '#1545A8',
                    borderRadius: 5,
                    borderSkipped: false,
                    maxBarThickness: 22
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
                        callbacks: {
                            label: function (context) {
                                return ` ${context.raw} berkas diunggah`;
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
                            font: {
                                size: 11,
                                family: "'Poppins', sans-serif"
                            },
                            color: '#94A3B8'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: {
                                size: 11,
                                family: "'Poppins', sans-serif"
                            },
                            color: '#94A3B8'
                        },
                        grid: {
                            color: '#F1F5F9',
                            drawBorder: false
                        }
                    }
                }
            }
        });
    }
});
</script>
