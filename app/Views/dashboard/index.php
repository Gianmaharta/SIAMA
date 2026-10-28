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

<!-- Main Stack: Top (Distribusi Arsip per OPD) & Bottom (Aktivitas Terbaru) -->
<div class="dashboard-stack">
    <!-- Panel Atas: Grafik Distribusi Arsip per OPD (Full Width) -->
    <div class="panel-card chart-panel">
        <div class="chart-panel-header">
            <div class="panel-title-block">
                <h2 class="panel-title">Distribusi Arsip per OPD</h2>
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
                <span class="small text-muted ms-2">Memperbarui grafik...</span>
            </div>
            
            <div id="chartContainer" class="chart-inner-container">
                <canvas id="arsipOpdChart"></canvas>
            </div>
        </div>

        <!-- Sumbu X Label Footer -->
        <div class="chart-axis-footer">
            <span class="axis-label-text">Jumlah Arsip</span>
        </div>
    </div>

    <!-- Panel Bawah: Aktivitas Terbaru (Full Width) -->
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
                                    <span class="activity-action" title="<?= esc($act['title'] ?? 'Aktivitas Sistem') ?>"><?= esc($act['title'] ?? 'Aktivitas Sistem') ?></span>
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
     * AJAX Fetch untuk memperbarui data grafik saat filter diubah
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
});
</script>
<?= $this->endSection() ?>
