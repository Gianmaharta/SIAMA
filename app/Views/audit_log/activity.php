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
    <p class="act-header-desc">Kelola data aktivitas pengguna pada sistem SIAMA.</p>
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
