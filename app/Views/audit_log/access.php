<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Access Log - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/access-master.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- HEADER HALAMAN -->
<div class="acc-header-wrapper">
    <h1 class="acc-header-title">Access Log</h1>
    <p class="acc-header-desc">Kelola data aktivitas akses pengguna pada sistem SIAMA.</p>
</div>

<!-- CARD UTAMA: FILTER & TABEL ACCESS LOG -->
<div class="card border-0 shadow-sm acc-card">
    <!-- PANEL FILTER -->
    <form method="GET" action="<?= base_url('/audit-log/access') ?>" id="formFilterAccess" class="mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <!-- Filter Kiri: Search, Filter Aksi, Filter Periode -->
            <div class="d-flex align-items-center flex-wrap gap-3 flex-grow-1">
                <!-- 1. Search Input -->
                <div class="acc-search-wrapper">
                    <i class="bi bi-search acc-search-icon"></i>
                    <input type="text"
                           name="search"
                           class="form-control acc-search-input"
                           placeholder="Cari username atau IP..."
                           value="<?= esc($filters['search'] ?? '') ?>">
                </div>

                <!-- 2. Filter Aksi -->
                <div class="d-flex align-items-center gap-2">
                    <label for="filterAksi" class="acc-select-label mb-0">Aksi</label>
                    <select name="action" id="filterAksi" class="form-select acc-select" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        <option value="Login" <?= ($filters['action'] ?? '') === 'Login' ? 'selected' : '' ?>>Login</option>
                        <option value="Logout" <?= ($filters['action'] ?? '') === 'Logout' ? 'selected' : '' ?>>Logout</option>
                    </select>
                </div>

                <!-- 3. Filter Periode -->
                <div class="d-flex align-items-center gap-2">
                    <label for="filterPeriode" class="acc-select-label mb-0">Periode</label>
                    <select name="periode" id="filterPeriode" class="form-select acc-select" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        <option value="today" <?= ($filters['periode'] ?? '') === 'today' ? 'selected' : '' ?>>Hari Ini</option>
                        <option value="week" <?= ($filters['periode'] ?? '') === 'week' ? 'selected' : '' ?>>7 Hari Terakhir</option>
                        <option value="month_30" <?= in_array($filters['periode'] ?? '', ['month', 'month_30']) ? 'selected' : '' ?>>30 Hari Terakhir</option>
                    </select>
                </div>
            </div>

            <!-- Tombol Export CSV (Rata Kanan) -->
            <div class="d-flex align-items-center">
                <?php
                    $exportQuery = http_build_query([
                        'search'  => $filters['search'] ?? '',
                        'action'  => $filters['action'] ?? '',
                        'periode' => $filters['periode'] ?? '',
                    ]);
                ?>
                <a href="<?= base_url('/audit-log/access/export?' . $exportQuery) ?>" class="btn btn-outline-secondary acc-btn-export" title="Unduh data dalam format CSV">
                    <i class="bi bi-download"></i>
                    <span>Export CSV</span>
                </a>
            </div>
        </div>
    </form>

    <!-- TABEL ACCESS LOG -->
    <div class="table-responsive">
        <table class="table acc-table align-middle">
            <thead>
                <tr>
                    <th class="acc-col-no">NO</th>
                    <th>WAKTU</th>
                    <th>USERNAME</th>
                    <th>IP ADDRESS</th>
                    <th>AKSI</th>
                    <th>KETERANGAN</th>
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
                            // Tentukan Username
                            $username = 'system';
                            if (!empty($row['nama_user'])) {
                                $cleanName = preg_replace('/\s*\(.*?\)\s*/', '', $row['nama_user']);
                                $username = strtolower(str_replace(' ', '.', trim($cleanName)));
                            } elseif (!empty($row['email'])) {
                                $parts = explode('@', $row['email']);
                                $username = $parts[0];
                            }
                            if ($username === 'administrator.pemkab' || $username === 'admin.pemkab') {
                                $username = 'admin_pemkab';
                            }

                            // Tentukan Aksi & Badge
                            $rawEvent = strtolower($row['event'] ?? '');
                            $aksiBadgeClass = 'badge-aksi-login';
                            $aksiLabel = 'Login';
                            $keterangan = 'Aktivitas akses sistem';

                            // Deteksi browser dari user_agent
                            $browser = 'Chrome';
                            $ua = $row['user_agent'] ?? '';
                            if (!empty($ua)) {
                                if (stripos($ua, 'Edg') !== false) {
                                    $browser = 'Edge';
                                } elseif (stripos($ua, 'Firefox') !== false) {
                                    $browser = 'Firefox';
                                } elseif (stripos($ua, 'Safari') !== false && stripos($ua, 'Chrome') === false) {
                                    $browser = 'Safari';
                                } elseif (stripos($ua, 'Chrome') !== false) {
                                    $browser = 'Chrome';
                                }
                            }

                            if (str_contains($rawEvent, 'login') && !str_contains($rawEvent, 'fail')) {
                                $aksiBadgeClass = 'badge-aksi-login';
                                $aksiLabel = 'Login';
                                $keterangan = 'Login berhasil dari browser ' . $browser;
                            } elseif (str_contains($rawEvent, 'logout')) {
                                $aksiBadgeClass = 'badge-aksi-logout';
                                $aksiLabel = 'Logout';
                                $keterangan = 'Sesi berakhir oleh pengguna';
                            } elseif (str_contains($rawEvent, 'fail')) {
                                $aksiBadgeClass = 'badge-aksi-failed';
                                $aksiLabel = 'Gagal';
                                $keterangan = 'Percobaan login gagal (kredensial tidak valid)';
                            } else {
                                $aksiBadgeClass = 'badge-aksi-logout';
                                $aksiLabel = ucfirst($row['event']);
                                $keterangan = 'Aktivitas akses sistem';
                            }

                            $ipDisplay = $row['ip_address'] === '::1' ? '127.0.0.1' : $row['ip_address'];
                        ?>
                        <tr>
                            <td class="acc-col-no"><?= $no++ ?></td>
                            <td class="acc-time"><?= esc($row['created_at']) ?></td>
                            <td class="acc-username fw-semibold"><?= esc($username) ?></td>
                            <td class="acc-ip"><?= esc($ipDisplay) ?></td>
                            <td>
                                <span class="badge-aksi <?= $aksiBadgeClass ?>">
                                    <span class="aksi-dot"></span>
                                    <span><?= esc($aksiLabel) ?></span>
                                </span>
                            </td>
                            <td>
                                <div class="acc-detail" title="<?= esc($keterangan) ?>">
                                    <?= esc($keterangan) ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            Tidak ada data aktivitas akses yang sesuai dengan filter.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- FOOTER TABEL: INFO ENTRI & PAGINASI -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 acc-table-footer">
        <div class="acc-entries-info">
            <?php if (!empty($total) && $total > 0) : ?>
                <?php 
                    $currCount = count($logs);
                    if ($total <= ($perPage ?? 10)) {
                        $infoText = "Menampilkan {$total} dari {$total} entri";
                    } else {
                        $firstItem = $startNumber;
                        $lastItem = min($startNumber + $currCount - 1, $total);
                        $infoText = "Menampilkan {$firstItem}–{$lastItem} dari " . number_format($total) . " entri";
                    }
                ?>
                <?= $infoText ?>
            <?php else : ?>
                Menampilkan 0 dari 0 entri
            <?php endif; ?>
        </div>

        <div class="acc-pagination">
            <?php
            $totalPages = ceil(($total ?? 0) / ($perPage ?? 10));
            if ($totalPages < 1) $totalPages = 1;
            $currentPage = $page ?? 1;
            $queryParams = $_GET;
            ?>
            <nav aria-label="Navigasi Halaman Access Log">
                <ul class="pagination mb-0">
                    <!-- Tombol Previous ‹ -->
                    <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                        <?php
                        $prevQuery = array_merge($queryParams, ['page' => max(1, $currentPage - 1)]);
                        ?>
                        <a class="page-link" href="<?= $currentPage <= 1 ? '#' : base_url('/audit-log/access?' . http_build_query($prevQuery)) ?>" aria-label="Sebelumnya">
                            ‹
                        </a>
                    </li>

                    <!-- Nomor Halaman -->
                    <?php
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);

                    if ($totalPages <= 5) {
                        $startPage = 1;
                        $endPage = $totalPages;
                    }
                    ?>

                    <?php for ($p = $startPage; $p <= $endPage; $p++) : ?>
                        <?php $pQuery = array_merge($queryParams, ['page' => $p]); ?>
                        <li class="page-item <?= ($p == $currentPage) ? 'active' : '' ?>">
                            <a class="page-link" href="<?= base_url('/audit-log/access?' . http_build_query($pQuery)) ?>">
                                <?= $p ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- Tombol Next › -->
                    <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                        <?php
                        $nextQuery = array_merge($queryParams, ['page' => min($totalPages, $currentPage + 1)]);
                        ?>
                        <a class="page-link" href="<?= $currentPage >= $totalPages ? '#' : base_url('/audit-log/access?' . http_build_query($nextQuery)) ?>" aria-label="Selanjutnya">
                            ›
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
