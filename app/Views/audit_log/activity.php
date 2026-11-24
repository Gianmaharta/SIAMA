<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Activity Log - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/activity-master.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- HEADER HALAMAN -->
<div class="act-header-wrapper">
    <h1 class="act-header-title">Activity Log</h1>
    <p class="act-header-desc">Visualisasi grafik dan rekam jejak aktivitas pengguna pada sistem SIAMA.</p>
</div>

<!-- PANEL VISUALISASI GRAFIK ACTIVITY LOG (Catatan #11) -->
<div class="row g-3 g-xl-4 mb-4">
    <!-- Grafik 1: Tren Aktivitas Harian -->
    <div class="col-12 col-xl-7">
        <div class="act-card h-100 p-3 p-md-4 mb-0">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 15.5px;">
                        <i class="bi bi-graph-up-arrow text-primary me-1"></i>
                        Tren Aktivitas Harian Pengguna
                    </h5>
                    <p class="text-secondary small mb-0" style="font-size: 12px;">Volume aktivitas operasional sistem per hari.</p>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1" style="font-size: 11.5px;">
                    Total: <?= number_format($total ?? 0, 0, ',', '.') ?> Aktivitas
                </span>
            </div>
            <div style="height: 230px; position: relative;">
                <canvas id="activityTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Grafik 2: Distribusi Aktivitas per Modul -->
    <div class="col-12 col-xl-5">
        <div class="act-card h-100 p-3 p-md-4 mb-0">
            <div class="mb-3">
                <h5 class="fw-bold text-dark mb-1" style="font-size: 15.5px;">
                    <i class="bi bi-pie-chart-fill text-primary me-1"></i>
                    Distribusi Aktivitas per Modul
                </h5>
                <p class="text-secondary small mb-0" style="font-size: 12px;">Perbandingan frekuensi aktivitas antar modul aplikasi.</p>
            </div>
            <div style="height: 230px; position: relative;">
                <canvas id="activityModuleChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- CARD UTAMA: FILTER & TABEL ACTIVITY LOG -->
<div class="act-card">
    <!-- PANEL FILTER -->
    <form method="GET" action="<?= base_url('/audit-log/activity') ?>" id="formFilterActivity" class="mb-4">
        <div class="act-filter-row mb-3">
            <!-- 1. Search Input -->
            <div class="act-search-col">
                <div class="act-search-wrapper">
                    <i class="bi bi-search act-search-icon"></i>
                    <input type="text"
                           name="search"
                           class="form-control act-search-input"
                           placeholder="Cari username, modul, atau aktivitas..."
                           value="<?= esc($filters['search'] ?? '') ?>">
                </div>
            </div>

            <!-- 2. Filter Modul -->
            <div class="act-select-group">
                <label for="filterModule" class="act-select-label">Modul</label>
                <select name="module" id="filterModule" class="form-select act-select" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <?php 
                    $defaultModules = [
                        'Master OPD', 
                        'Master Bidang', 
                        'Penilaian Arsip', 
                        'Pengelolaan Arsip', 
                        'SPT', 
                        'Berita Acara', 
                        'Manajemen Akun'
                    ];
                    $dbModules = !empty($modules) ? array_column($modules, 'module') : [];
                    $allModules = array_unique(array_merge($defaultModules, $dbModules));
                    ?>
                    <?php foreach ($allModules as $mod) : ?>
                        <option value="<?= esc($mod) ?>" <?= ($filters['module'] ?? '') === $mod ? 'selected' : '' ?>>
                            <?= esc($mod) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 3. Filter Status -->
            <div class="act-select-group">
                <label for="filterStatus" class="act-select-label">Status</label>
                <select name="status" id="filterStatus" class="form-select act-select" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="Berhasil" <?= ($filters['status'] ?? '') === 'Berhasil' ? 'selected' : '' ?>>Berhasil</option>
                    <option value="Gagal" <?= ($filters['status'] ?? '') === 'Gagal' ? 'selected' : '' ?>>Gagal</option>
                </select>
            </div>

            <!-- 4. Filter Periode -->
            <div class="act-select-group">
                <label for="filterPeriode" class="act-select-label">Periode</label>
                <select name="periode" id="filterPeriode" class="form-select act-select" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="today" <?= ($filters['periode'] ?? '') === 'today' ? 'selected' : '' ?>>Hari Ini</option>
                    <option value="week" <?= ($filters['periode'] ?? '') === 'week' ? 'selected' : '' ?>>7 Hari Terakhir</option>
                    <option value="month" <?= ($filters['periode'] ?? '') === 'month' ? 'selected' : '' ?>>Bulan Ini</option>
                    <option value="year" <?= ($filters['periode'] ?? '') === 'year' ? 'selected' : '' ?>>Tahun Ini</option>
                </select>
            </div>
        </div>

        <!-- Tombol Export CSV (Rata Kanan) -->
        <div class="d-flex justify-content-end">
            <?php
                $exportQuery = http_build_query([
                    'search'  => $filters['search'] ?? '',
                    'module'  => $filters['module'] ?? '',
                    'status'  => $filters['status'] ?? '',
                    'periode' => $filters['periode'] ?? '',
                ]);
            ?>
            <a href="<?= base_url('/audit-log/activity/export?' . $exportQuery) ?>" class="act-btn-export" title="Unduh data dalam format CSV">
                <i class="bi bi-download"></i>
                <span>Export CSV</span>
            </a>
        </div>
    </form>

    <!-- TABEL ACTIVITY LOG -->
    <div class="table-responsive">
        <table class="table act-table align-middle">
            <thead>
                <tr>
                    <th class="act-col-no">NO</th>
                    <th>WAKTU</th>
                    <th>USERNAME</th>
                    <th>MODUL</th>
                    <th>AKSI</th>
                    <th>DETAIL</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($logs)) : ?>
                    <?php
                    $startNumber = (($page ?? 1) - 1) * ($perPage ?? 10) + 1;
                    $no = $startNumber;
                    ?>
                    <?php foreach ($logs as $row) : ?>
                        <?php
                            // Tentukan username
                            $username = 'system';
                            if (!empty($row['email'])) {
                                $parts = explode('@', $row['email']);
                                $username = $parts[0];
                            } elseif (!empty($row['nama_user'])) {
                                $username = strtolower(str_replace(' ', '_', $row['nama_user']));
                            }

                            // Mapping class warna badge modul
                            $mod = $row['module'] ?? '';
                            $badgeClass = 'badge-modul-default';
                            if (stripos($mod, 'OPD') !== false) {
                                $badgeClass = 'badge-modul-opd';
                            } elseif (stripos($mod, 'Penilaian') !== false) {
                                $badgeClass = 'badge-modul-penilaian';
                            } elseif (stripos($mod, 'Pengelolaan') !== false || stripos($mod, 'Arsip') !== false) {
                                $badgeClass = 'badge-modul-arsip';
                            } elseif (stripos($mod, 'SPT') !== false) {
                                $badgeClass = 'badge-modul-spt';
                            } elseif (stripos($mod, 'Berita Acara') !== false || stripos($mod, 'BA') !== false) {
                                $badgeClass = 'badge-modul-ba';
                            } elseif (stripos($mod, 'User') !== false || stripos($mod, 'Akun') !== false) {
                                $badgeClass = 'badge-modul-user';
                            }
                        ?>
                        <tr>
                            <td class="act-col-no"><?= $no++ ?></td>
                            <td class="act-time"><?= esc($row['created_at']) ?></td>
                            <td class="act-username"><?= esc($username) ?></td>
                            <td>
                                <span class="badge-modul <?= $badgeClass ?>">
                                    <?= esc($row['module']) ?>
                                </span>
                            </td>
                            <td class="act-action"><?= esc($row['action']) ?></td>
                            <td>
                                <div class="act-detail" title="<?= esc($row['description']) ?>">
                                    <?= esc($row['description']) ?>
                                </div>
                            </td>
                            <td>
                                <span class="act-badge-status act-badge-status-success">
                                    <span class="act-status-dot-success"></span>
                                    <span>Berhasil</span>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            Tidak ada data aktivitas yang sesuai dengan filter.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- FOOTER TABEL: INFO ENTRI & PAGINASI -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 act-table-footer">
        <div class="act-entries-info">
            <?php if (!empty($total) && $total > 0) : ?>
                <?php 
                    $currCount = count($logs);
                    $firstItem = $startNumber;
                    $lastItem = min($startNumber + $currCount - 1, $total);
                ?>
                Menampilkan <?= $firstItem ?>–<?= $lastItem ?> dari <?= number_format($total) ?> entri
            <?php else : ?>
                Menampilkan 0 dari 0 entri
            <?php endif; ?>
        </div>
        <div class="act-pagination">
            <?php if (!empty($pager)) : ?>
                <?= $pager ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    const chartStats = <?= json_encode($chart_stats ?? [
        'daily_trend' => ['labels' => [], 'values' => []],
        'by_module'   => ['labels' => [], 'values' => []],
        'by_action'   => ['labels' => [], 'values' => []]
    ]) ?>;

    const trendEl = document.getElementById('activityTrendChart');
    if (trendEl) {
        new Chart(trendEl.getContext('2d'), {
            type: 'line',
            data: {
                labels: chartStats.daily_trend.labels || [],
                datasets: [{
                    label: 'Jumlah Aktivitas',
                    data: chartStats.daily_trend.values || [],
                    borderColor: '#1E60EC',
                    backgroundColor: 'rgba(30, 96, 236, 0.12)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#1E60EC',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    const modEl = document.getElementById('activityModuleChart');
    if (modEl) {
        new Chart(modEl.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: chartStats.by_module.labels || [],
                datasets: [{
                    data: chartStats.by_module.values || [],
                    backgroundColor: ['#1E60EC', '#10B981', '#F59E0B', '#7C3AED', '#EC4899', '#06B6D4', '#64748B', '#EF4444'],
                    borderWidth: 2,
                    borderColor: '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 12, font: { family: 'Poppins', size: 11.5 } }
                    }
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
