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

<!-- Welcome Section -->
<div class="welcome-section">
    <h1 class="welcome-title">Selamat Datang, <?= esc($nama_lengkap) ?>!</h1>
    <p class="welcome-subtitle">Ini adalah ringkasan statistik berdasarkan akses Anda sebagai <?= esc(str_replace('_', ' ', $nama_role)) ?>.</p>
</div>

<!-- Statistic Cards -->
<?php if ($nama_role == 'Admin_Pemkab') : ?>
    <div class="stats-grid">
        <!-- Card 1: Total OPD -->
        <div class="stat-card card-blue">
            <div class="stat-icon-box">
                <i class="bi bi-bank2"></i>
            </div>
            <div>
                <div class="stat-label">Total OPD</div>
                <div class="stat-value"><?= esc($stats['total_opd'] ?? 0) ?></div>
            </div>
        </div>

        <!-- Card 2: Total Seluruh Pengguna -->
        <div class="stat-card card-green">
            <div class="stat-icon-box">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="stat-label">Total Seluruh Pengguna</div>
                <div class="stat-value"><?= esc($stats['total_users'] ?? 0) ?></div>
            </div>
        </div>

        <!-- Card 3: Total Arsip Global -->
        <div class="stat-card card-purple">
            <div class="stat-icon-box">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div>
                <div class="stat-label">Total Arsip Global</div>
                <div class="stat-value"><?= esc($stats['total_arsip'] ?? 0) ?></div>
            </div>
        </div>

        <!-- Card 4: Total SPT Global -->
        <div class="stat-card card-amber">
            <div class="stat-icon-box">
                <i class="bi bi-file-text-fill"></i>
            </div>
            <div>
                <div class="stat-label">Total SPT Global</div>
                <div class="stat-value"><?= esc($stats['total_spt'] ?? 0) ?></div>
            </div>
        </div>

        <!-- Card 5: Total Berita Acara Global -->
        <div class="stat-card card-rose">
            <div class="stat-icon-box">
                <i class="bi bi-file-earmark-check-fill"></i>
            </div>
            <div>
                <div class="stat-label">Total Berita Acara Global</div>
                <div class="stat-value"><?= esc($stats['total_berita_acara'] ?? 0) ?></div>
            </div>
        </div>
    </div>

<?php elseif ($nama_role == 'Pimpinan') : ?>
    <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr);">
        <div class="stat-card card-blue">
            <div class="stat-icon-box">
                <i class="bi bi-file-text-fill"></i>
            </div>
            <div>
                <div class="stat-label">Total SPT Diterbitkan</div>
                <div class="stat-value"><?= esc($stats['spt_diterbitkan'] ?? 0) ?></div>
            </div>
        </div>

        <div class="stat-card card-rose">
            <div class="stat-icon-box">
                <i class="bi bi-pen-fill"></i>
            </div>
            <div>
                <div class="stat-label">Berita Acara Menanti TTD Pimpinan</div>
                <div class="stat-value"><?= esc($stats['ba_menanti_pimpinan'] ?? 0) ?></div>
            </div>
        </div>
    </div>

<?php elseif ($nama_role == 'Admin_OPD') : ?>
    <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
        <div class="stat-card card-blue">
            <div class="stat-icon-box">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="stat-label">Total Pengguna di OPD Anda</div>
                <div class="stat-value"><?= esc($stats['total_users_opd'] ?? 0) ?></div>
            </div>
        </div>

        <div class="stat-card card-green">
            <div class="stat-icon-box">
                <i class="bi bi-diagram-3-fill"></i>
            </div>
            <div>
                <div class="stat-label">Total Bidang di OPD Anda</div>
                <div class="stat-value"><?= esc($stats['total_bidang_opd'] ?? 0) ?></div>
            </div>
        </div>

        <div class="stat-card card-purple">
            <div class="stat-icon-box">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div>
                <div class="stat-label">Total Arsip di OPD Anda</div>
                <div class="stat-value"><?= esc($stats['total_arsip_opd'] ?? 0) ?></div>
            </div>
        </div>
    </div>

<?php elseif ($nama_role == 'Kepala_Bidang') : ?>
    <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr);">
        <div class="stat-card card-purple">
            <div class="stat-icon-box">
                <i class="bi bi-folder-fill"></i>
            </div>
            <div>
                <div class="stat-label">Total Arsip di Bidang Anda</div>
                <div class="stat-value"><?= esc($stats['total_arsip_bidang'] ?? 0) ?></div>
            </div>
        </div>

        <div class="stat-card card-rose">
            <div class="stat-icon-box">
                <i class="bi bi-check2-square"></i>
            </div>
            <div>
                <div class="stat-label">Berita Acara Menanti Verifikasi Kabid</div>
                <div class="stat-value"><?= esc($stats['ba_menanti_kabid'] ?? 0) ?></div>
            </div>
        </div>
    </div>

<?php elseif ($nama_role == 'Arsiparis') : ?>
    <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr);">
        <div class="stat-card card-purple">
            <div class="stat-icon-box">
                <i class="bi bi-cloud-arrow-up-fill"></i>
            </div>
            <div>
                <div class="stat-label">Total Arsip yang Anda Unggah</div>
                <div class="stat-value"><?= esc($stats['arsip_diunggah'] ?? 0) ?></div>
            </div>
        </div>

        <div class="stat-card card-amber">
            <div class="stat-icon-box">
                <i class="bi bi-card-checklist"></i>
            </div>
            <div>
                <div class="stat-label">Penugasan SPT Anda</div>
                <div class="stat-value"><?= esc($stats['penugasan_spt'] ?? 0) ?></div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Bottom Section: Graph (70%) & Recent Activity (30%) -->
<div class="dashboard-bottom-grid">
    <!-- Left Column: Graph -->
    <div class="panel-card">
        <div class="chart-title-area">
            <h2 class="chart-title">Grafik</h2>
            <div class="chart-title-line">
                <div class="chart-title-accent"></div>
            </div>
        </div>

        <div class="chart-canvas-wrapper">
            <canvas id="arsipOpdChart"></canvas>
        </div>

        <p class="chart-subtitle">Distribusi Arsip Per OPD</p>
    </div>

    <!-- Right Column: Recent Activities -->
    <div class="panel-card">
        <div class="activity-header">
            <h2 class="activity-title">Aktivitas Terbaru</h2>
            <a href="<?= base_url('/audit-log/activity') ?>" class="activity-more-link">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="activity-list">
            <?php if (!empty($recent_activities)) : ?>
                <?php 
                $icons = [
                    'icon-green'  => 'bi-plus-lg',
                    'icon-blue'   => 'bi-person',
                    'icon-amber'  => 'bi-file-earmark',
                    'icon-purple' => 'bi-folder',
                ];
                $colorKeys = array_keys($icons);
                ?>
                <?php foreach ($recent_activities as $idx => $act) : ?>
                    <?php 
                        $colorClass = $colorKeys[$idx % count($colorKeys)];
                        $iconClass  = $icons[$colorClass];
                        $actDate    = date('d M Y', strtotime($act['created_at']));
                        $actTime    = date('H:i', strtotime($act['created_at']));
                    ?>
                    <div class="activity-item">
                        <div class="activity-left">
                            <div class="activity-icon <?= $colorClass ?>">
                                <i class="bi <?= $iconClass ?>"></i>
                            </div>
                            <div class="activity-desc">
                                <span class="activity-action"><?= esc($act['action'] ?? 'Aktivitas Sistem') ?></span>
                                <span class="activity-user">Oleh <?= esc($act['nama_user'] ?? 'Administrator') ?></span>
                            </div>
                        </div>
                        <div class="activity-time">
                            <span><?= $actDate ?></span>
                            <span><?= $actTime ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <!-- Default Sample Activity Items matching mockup -->
                <div class="activity-item">
                    <div class="activity-left">
                        <div class="activity-icon icon-green">
                            <i class="bi bi-plus-lg"></i>
                        </div>
                        <div class="activity-desc">
                            <span class="activity-action">Menambahkan data OPD</span>
                            <span class="activity-user">Oleh Administrator Pemkab</span>
                        </div>
                    </div>
                    <div class="activity-time">
                        <span>21 Sep 2026</span>
                        <span>08:10</span>
                    </div>
                </div>

                <div class="activity-item">
                    <div class="activity-left">
                        <div class="activity-icon icon-blue">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="activity-desc">
                            <span class="activity-action">Menambahkan pengguna baru</span>
                            <span class="activity-user">Oleh Administrator Pemkab</span>
                        </div>
                    </div>
                    <div class="activity-time">
                        <span>21 Sep 2026</span>
                        <span>08:05</span>
                    </div>
                </div>

                <div class="activity-item">
                    <div class="activity-left">
                        <div class="activity-icon icon-amber">
                            <i class="bi bi-file-earmark"></i>
                        </div>
                        <div class="activity-desc">
                            <span class="activity-action">Mengunggah SPT</span>
                            <span class="activity-user">Oleh Kepala Dinas</span>
                        </div>
                    </div>
                    <div class="activity-time">
                        <span>21 Sep 2026</span>
                        <span>07:50</span>
                    </div>
                </div>

                <div class="activity-item">
                    <div class="activity-left">
                        <div class="activity-icon icon-purple">
                            <i class="bi bi-folder"></i>
                        </div>
                        <div class="activity-desc">
                            <span class="activity-action">Mengelola data arsip</span>
                            <span class="activity-user">Oleh Administrator Pemkab</span>
                        </div>
                    </div>
                    <div class="activity-time">
                        <span>21 Sep 2026</span>
                        <span>07:45</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('arsipOpdChart');
    if (!ctx) return;

    const labels = <?= json_encode($chart_labels ?? ['Kominfo', 'Disdik', 'Arsip', 'Dinkes']) ?>;
    const dataValues = <?= json_encode($chart_values ?? [56, 64, 76, 78]) ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: dataValues,
                backgroundColor: '#0B1528',
                hoverBackgroundColor: '#1E60EC',
                borderRadius: 4,
                borderSkipped: false,
                barThickness: 38,
                maxBarThickness: 45
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
                    backgroundColor: '#0B1528',
                    titleFont: { family: 'Poppins', size: 12 },
                    bodyFont: { family: 'Poppins', size: 12 },
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' Arsip';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: true,
                        borderColor: '#E5E7EB'
                    },
                    ticks: {
                        font: {
                            family: 'Poppins',
                            size: 12,
                            weight: '500'
                        },
                        color: '#667085',
                        padding: 8
                    }
                },
                y: {
                    beginAtZero: true,
                    suggestedMax: 100,
                    grid: {
                        color: '#E5E7EB',
                        borderDash: [5, 5],
                        drawBorder: false
                    },
                    ticks: {
                        stepSize: 20,
                        font: {
                            family: 'Poppins',
                            size: 12,
                            weight: '500'
                        },
                        color: '#667085',
                        padding: 10
                    }
                }
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
