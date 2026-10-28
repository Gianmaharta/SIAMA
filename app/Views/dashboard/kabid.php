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
?>

<div class="kabid-dashboard">
    <!-- 1. HEADING & SUBHEADING -->
    <div class="mb-4">
        <h1 class="kabid-header-title">Selamat Datang, <?= esc($nama_lengkap ?? 'Budi Santoso') ?>!</h1>
        <p class="kabid-header-desc">Ini adalah ringkasan statistik berdasarkan akses Anda sebagai Kepala Bidang</p>
    </div>

    <!-- 2. SUMMARY CARDS -->
    <div class="row g-4 mb-4">
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
                            <?= esc($stats['total_arsip_bidang'] ?? $totalArsip) ?>
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
                            <?= esc($stats['ba_menanti_kabid'] ?? 0) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. CONTENT SECTION: CHARTS -->
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

    <!-- 4. BERITA ACARA MENANTI VERIFIKASI KABID -->
    <div class="row">
        <div class="col-12">
            <div class="card kabid-card border-0">
                <div class="kabid-card-header">
                    <div class="kabid-card-title-group">
                        <i class="bi bi-clock kabid-card-icon"></i>
                        <h2 class="kabid-card-title">Berita Acara Menanti Verifikasi Kabid</h2>
                    </div>
                    <a href="<?= base_url('/berita-acara') ?>" class="kabid-link-all">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table kabid-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Nomor BA</th>
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

<!-- SCRIPT INISIALISASI GRAFIK CHART.JS -->
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
});
</script>
