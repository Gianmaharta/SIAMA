<?php
$sysSettings = $system_settings ?? [];
$siteName    = $sysSettings['site_name'] ?? 'SIAMA';
$pwMode      = $sysSettings['password_mode'] ?? 'default';
$defaultPw   = $sysSettings['default_password'] ?? 'Admin123!';
$pwModeLabel = ($pwMode === 'random') ? 'Password Random Otomatis' : 'Password Default Website';
?>

<style>
/* ======================================================
   SUPER ADMIN DASHBOARD — Clean Enterprise Design
   Poppins font, Bootstrap 5, Custom vars from dashboard.css
   ====================================================== */

/* ---- Header Panel ---- */
.sa-header {
    background: #FFFFFF;
    border: 1px solid #E5EAF1;
    border-radius: 12px;
    padding: 24px 28px;
    margin-bottom: 20px;
    box-shadow: 0 1px 4px rgba(15, 23, 42, 0.03);
}

.sa-header-greeting {
    font-size: 13.5px;
    font-weight: 500;
    color: #475569;
    margin-bottom: 6px;
}

.sa-header-title {
    font-size: 22px;
    font-weight: 700;
    color: #0B1528;
    margin: 0 0 6px 0;
    line-height: 1.3;
    letter-spacing: -0.3px;
}

.sa-header-desc {
    font-size: 13.5px;
    color: #64748B;
    line-height: 1.55;
    margin: 0;
}

.sa-header-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    height: 38px;
    font-size: 12.5px;
    font-weight: 600;
    padding: 0 16px;
    border-radius: 8px;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.18s ease;
}
.sa-header-btn-primary {
    background: #1E60EC;
    color: #FFFFFF;
    border: 1px solid #1E60EC;
}
.sa-header-btn-primary:hover {
    background: #154EC2;
    border-color: #154EC2;
    color: #FFFFFF;
}
.sa-header-btn-secondary {
    background: #FFFFFF;
    color: #172033;
    border: 1px solid #E5EAF1;
}
.sa-header-btn-secondary:hover {
    background: #F8FAFC;
    border-color: #CBD5E1;
    color: #0B1528;
}

/* ---- Metric Cards ---- */
.sa-metric-card {
    background: #FFFFFF;
    border: 1px solid #E5EAF1;
    border-radius: 12px;
    padding: 18px 20px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
    transition: transform 0.18s ease, box-shadow 0.18s ease;
}

.sa-metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.07);
}

.sa-metric-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 19px;
    flex-shrink: 0;
}

.sa-metric-num {
    font-size: 30px;
    font-weight: 700;
    color: #172033;
    line-height: 1;
    letter-spacing: -0.5px;
}

.sa-metric-label {
    font-size: 12px;
    font-weight: 500;
    color: #64748B;
    line-height: 1.35;
    margin-bottom: 4px;
}

.sa-metric-footer {
    padding-top: 10px;
    margin-top: 10px;
    border-top: 1px solid #F1F5F9;
    font-size: 12px;
}

/* ---- Panel Card (generic) ---- */
.sa-panel {
    background: #FFFFFF;
    border: 1px solid #E5EAF1;
    border-radius: 12px;
    padding: 20px 22px;
    box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
}

.sa-panel-title {
    font-size: 14.5px;
    font-weight: 700;
    color: #172033;
    margin: 0;
    line-height: 1.25;
}

.sa-panel-desc {
    font-size: 12px;
    color: #64748B;
    margin: 2px 0 0 0;
}

.sa-panel-divider {
    border: none;
    border-top: 1px solid #F1F5F9;
    margin: 14px 0;
}

/* ---- Table inside panels ---- */
.sa-table {
    font-size: 12.5px;
    margin: 0;
}
.sa-table thead th {
    font-size: 11.5px;
    font-weight: 600;
    color: #64748B;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    background: #F8FAFC;
    border-bottom: 1px solid #E5EAF1;
    padding: 8px 12px;
}
.sa-table tbody td {
    padding: 9px 12px;
    border-bottom: 1px solid #F1F5F9;
    vertical-align: middle;
    color: #172033;
}
.sa-table tbody tr:last-child td { border-bottom: none; }
.sa-table tbody tr:hover td { background: #FAFCFF; }

/* ---- Role badges ---- */
.sa-role-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.sa-role-super { background: #EEF5FE; color: #1E60EC; }
.sa-role-pemkab { background: #F0FDF4; color: #15803D; }
.sa-role-opd { background: #FFF7ED; color: #C2410C; }
.sa-role-pimpinan { background: #FDF4FF; color: #9333EA; }
.sa-role-arsiparis { background: #ECFDF5; color: #0D9488; }
.sa-role-default { background: #F1F5F9; color: #64748B; }

/* ---- Switch btn ---- */
.sa-switch-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: #1E60EC;
    color: #FFFFFF;
    border: none;
    text-decoration: none;
    font-size: 14px;
    transition: background 0.18s;
    flex-shrink: 0;
}
.sa-switch-btn:hover { background: #154EC2; color: #FFFFFF; }

/* ---- Access log / notification items ---- */
.sa-log-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 8px;
    background: #F8FAFC;
    border: 1px solid #E5EAF1;
    font-size: 12.5px;
}

.sa-log-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 9px;
    border-radius: 20px;
    font-size: 10.5px;
    font-weight: 600;
    white-space: nowrap;
    flex-shrink: 0;
}
.sa-log-success { background: #ECFDF5; color: #15803D; }
.sa-log-fail    { background: #FEF2F2; color: #DC2626; }
.sa-log-logout  { background: #F1F5F9; color: #64748B; }

/* ---- Activity audit grid ---- */
.sa-activity-item {
    background: #FFFFFF;
    border: 1px solid #E5EAF1;
    border-radius: 10px;
    padding: 14px 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    height: 100%;
}

.sa-activity-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}

.badge-status {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 600;
}

/* ---- Section separator label ---- */
.sa-section-label {
    font-size: 10.5px;
    font-weight: 600;
    color: #94A3B8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 10px;
}
.sa-section-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #F1F5F9;
}
</style>

<!-- =====================================================
     1. HEADER PUSAT KENDALI SUPER ADMIN
     ===================================================== -->
<div class="sa-header mb-4">
    <p class="sa-header-greeting">
        Selamat datang kembali, <?= esc($nama_lengkap ?? 'Super Administrator') ?>!
    </p>

    <h1 class="sa-header-title">
        Pusat Kendali Sistem &amp; Keamanan <?= esc($siteName) ?>
    </h1>

    <p class="sa-header-desc mb-3">
        Kelola konfigurasi global, kebijakan password, switch user lintas peran, serta pantau log keamanan sistem.
    </p>

    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?= base_url('/settings') ?>" class="sa-header-btn sa-header-btn-primary">
            <i class="bi bi-sliders"></i>
            <span>Pengaturan Website &amp; Password</span>
        </a>
        <a href="<?= base_url('/users') ?>" class="sa-header-btn sa-header-btn-secondary">
            <i class="bi bi-person-badge"></i>
            <span>Manajemen Akun &amp; Switch User</span>
        </a>
        <a href="<?= base_url('/swagger') ?>" class="sa-header-btn sa-header-btn-secondary">
            <i class="bi bi-braces-asterisk"></i>
            <span>API Swagger</span>
        </a>
        <a href="<?= base_url('/') ?>" target="_blank" class="sa-header-btn sa-header-btn-secondary">
            <i class="bi bi-globe2"></i>
            <span>Landing Page</span>
        </a>
    </div>
</div>

<!-- =====================================================
     2. EMPAT KARTU METRIK UTAMA
     ===================================================== -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Akun -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="sa-metric-card">
            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div>
                    <div class="sa-metric-label">Total Akun Terdaftar</div>
                    <div class="sa-metric-num"><?= number_format($stats['total_users'] ?? 0, 0, ',', '.') ?></div>
                </div>
                <div class="sa-metric-icon" style="background:#EEF5FE; color:#1E60EC;">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <div class="sa-metric-footer d-flex align-items-center justify-content-between">
                <span class="text-success fw-semibold small"><i class="bi bi-check-circle me-1"></i><?= (int)($stats['active_users'] ?? 0) ?> Aktif</span>
                <span class="text-danger small fw-medium"><i class="bi bi-x-circle me-1"></i><?= (int)($stats['inactive_users'] ?? 0) ?> Nonaktif</span>
            </div>
        </div>
    </div>

    <!-- Card 2: Password Default -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="sa-metric-card">
            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div>
                    <div class="sa-metric-label">Akun Password Default</div>
                    <div class="sa-metric-num"><?= number_format($stats['default_pw_users'] ?? 0, 0, ',', '.') ?></div>
                </div>
                <div class="sa-metric-icon" style="background:#FFFBEB; color:#D97706;">
                    <i class="bi bi-key-fill"></i>
                </div>
            </div>
            <div class="sa-metric-footer d-flex align-items-center justify-content-between">
                <span class="text-secondary small">Default: <code class="fw-bold text-dark"><?= esc($defaultPw) ?></code></span>
                <a href="<?= base_url('/settings') ?>" class="text-primary text-decoration-none fw-semibold small">Ubah &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Card 3: Access Log -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="sa-metric-card">
            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div>
                    <div class="sa-metric-label">Total Log Akses (Login)</div>
                    <div class="sa-metric-num"><?= number_format($stats['total_access_logs'] ?? 0, 0, ',', '.') ?></div>
                </div>
                <div class="sa-metric-icon" style="background:#ECFDF5; color:#059669;">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
            </div>
            <div class="sa-metric-footer d-flex align-items-center justify-content-between">
                <span class="text-success fw-medium small"><?= number_format($stats['login_success_count'] ?? 0, 0, ',', '.') ?> Sukses</span>
                <span class="text-danger fw-semibold small"><?= number_format($stats['login_failed_count'] ?? 0, 0, ',', '.') ?> Gagal</span>
            </div>
        </div>
    </div>

    <!-- Card 4: Activity Log -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="sa-metric-card">
            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div>
                    <div class="sa-metric-label">Total Jejak Activity Log</div>
                    <div class="sa-metric-num"><?= number_format($stats['total_activity_logs'] ?? 0, 0, ',', '.') ?></div>
                </div>
                <div class="sa-metric-icon" style="background:#F5F3FF; color:#7C3AED;">
                    <i class="bi bi-activity"></i>
                </div>
            </div>
            <div class="sa-metric-footer d-flex align-items-center justify-content-between">
                <span class="text-secondary small"><strong class="text-dark"><?= number_format($stats['today_activity_logs'] ?? 0, 0, ',', '.') ?></strong> aksi hari ini</span>
                <a href="<?= base_url('/audit-log/activity') ?>" class="text-primary text-decoration-none fw-semibold small">Detail &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- =====================================================
     3. GRAFIK TREN AKTIVITAS & DISTRIBUSI ROLE
     ===================================================== -->
<div class="row g-3 mb-4">
    <!-- Grafik Tren Aktivitas (8 kolom) -->
    <div class="col-12 col-xl-8">
        <div class="sa-panel h-100">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                <div>
                    <p class="sa-panel-title">Tren Aktivitas Sistem &amp; Keamanan Akses</p>
                    <p class="sa-panel-desc">7 hari terakhir — transaksi sistem, login berhasil, dan percobaan login gagal.</p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <a href="<?= base_url('/audit-log/activity') ?>" class="btn btn-sm btn-light border rounded-3 fw-medium" style="font-size:12px;">Activity Log</a>
                    <a href="<?= base_url('/audit-log/access') ?>" class="btn btn-sm btn-light border rounded-3 fw-medium" style="font-size:12px;">Access Log</a>
                </div>
            </div>
            <div style="height: 260px; position: relative;">
                <canvas id="saTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Distribusi Role (4 kolom) -->
    <div class="col-12 col-xl-4">
        <div class="sa-panel h-100">
            <div class="mb-3">
                <p class="sa-panel-title">Komposisi Hierarki Role Pengguna</p>
                <p class="sa-panel-desc">Distribusi akun dari Super Admin hingga Arsiparis.</p>
            </div>
            <div style="height: 200px; position: relative;">
                <canvas id="saRoleChart"></canvas>
            </div>
            <hr class="sa-panel-divider">
            <div class="d-flex align-items-center justify-content-between small text-secondary">
                <span>Hierarki Tertinggi: <strong class="text-dark">Super Admin</strong></span>
                <a href="<?= base_url('/users') ?>" class="text-primary text-decoration-none fw-semibold">Kelola Role &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- =====================================================
     4. QUICK SWITCH USER & LOG AKSES / NOTIFIKASI
     ===================================================== -->
<div class="row g-3 mb-4">
    <!-- Quick Switch User -->
    <div class="col-12 col-xl-6">
        <div class="sa-panel h-100">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                <div>
                    <p class="sa-panel-title"><i class="bi bi-person-bounding-box text-primary me-1"></i>Quick Switch User</p>
                    <p class="sa-panel-desc">Hak eksklusif Super Admin untuk beralih sesi ke akun pengguna lain.</p>
                </div>
                <a href="<?= base_url('/users') ?>" class="btn btn-sm btn-outline-primary rounded-3 fw-medium flex-shrink-0" style="font-size:12px;">
                    Semua Akun (<?= (int)($stats['total_users'] ?? 0) ?>)
                </a>
            </div>

            <?php if (!empty($quick_switch_users)) : ?>
                <div class="table-responsive">
                    <table class="table sa-table align-middle">
                        <thead>
                            <tr>
                                <th>Pengguna</th>
                                <th>Role</th>
                                <th>OPD</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($quick_switch_users as $qu) :
                                $roleKey = strtolower(str_replace([' ', '_'], '', $qu['nama_role'] ?? ''));
                                $roleCls = match(true) {
                                    str_contains($roleKey, 'super')     => 'sa-role-super',
                                    str_contains($roleKey, 'pemkab')    => 'sa-role-pemkab',
                                    str_contains($roleKey, 'opd')       => 'sa-role-opd',
                                    str_contains($roleKey, 'pimpinan')
                                        || str_contains($roleKey, 'kepala') => 'sa-role-pimpinan',
                                    str_contains($roleKey, 'arsiparis') => 'sa-role-arsiparis',
                                    default                             => 'sa-role-default',
                                };
                            ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark" style="font-size:12.5px;"><?= esc($qu['nama']) ?></div>
                                        <div class="text-muted" style="font-size:11px;"><?= esc($qu['email']) ?></div>
                                    </td>
                                    <td>
                                        <span class="sa-role-badge <?= $roleCls ?>">
                                            <?= esc(str_replace('_', ' ', $qu['nama_role'] ?? '-')) ?>
                                        </span>
                                    </td>
                                    <td class="text-secondary" style="font-size:12px; text-align: justify;">
                                        <?= esc($qu['nama_opd'] ?? 'Tingkat Pemkab') ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= base_url('/users/switch/' . $qu['id_user']) ?>"
                                           class="sa-switch-btn"
                                           data-bs-toggle="tooltip"
                                           title="Masuk Sebagai <?= esc($qu['nama']) ?>"
                                           aria-label="Switch ke <?= esc($qu['nama']) ?>">
                                            <i class="bi bi-box-arrow-in-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="text-center py-5 text-secondary small">
                    <i class="bi bi-people fs-2 d-block mb-2 opacity-25"></i>
                    Belum ada akun pengguna lain yang terdaftar.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Log Akses & Notifikasi -->
    <div class="col-12 col-xl-6">
        <div class="sa-panel h-100">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                <div>
                    <p class="sa-panel-title"><i class="bi bi-shield-exclamation text-primary me-1"></i>Log Akses &amp; Notifikasi Terbaru</p>
                    <p class="sa-panel-desc">Pantauan real-time autentikasi pengguna dan notifikasi sistem.</p>
                </div>
                <a href="<?= base_url('/audit-log/access') ?>" class="btn btn-sm btn-outline-secondary rounded-3 fw-medium flex-shrink-0" style="font-size:12px;">Lihat Access Log</a>
            </div>

            <!-- Riwayat Autentikasi -->
            <div class="sa-section-label"><i class="bi bi-key"></i>Riwayat Autentikasi (Access Log)</div>
            <?php if (!empty($recent_access_logs)) : ?>
                <div class="d-flex flex-column gap-2 mb-3">
                    <?php foreach (array_slice($recent_access_logs, 0, 4) as $al) :
                        $st = strtolower((string)($al['status'] ?? 'login'));
                        $isFail = ($st === 'failed_login');
                        $isLogout = ($st === 'logout');
                        $logCls  = $isFail ? 'sa-log-fail' : ($isLogout ? 'sa-log-logout' : 'sa-log-success');
                        $logTxt  = $isFail ? 'Gagal Login' : ($isLogout ? 'Logout' : 'Login Sukses');
                    ?>
                        <div class="sa-log-item">
                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                <span class="sa-log-badge <?= $logCls ?>"><?= $logTxt ?></span>
                                <span class="text-dark fw-semibold text-truncate" style="font-size:12.5px;">
                                    <?= esc($al['nama_user'] ?? 'Unknown') ?>
                                    <span class="text-muted fw-normal">(<?= esc(str_replace('_', ' ', $al['nama_role'] ?? '-')) ?>)</span>
                                </span>
                            </div>
                            <span class="text-muted flex-shrink-0" style="font-size:11px;">
                                <?= !empty($al['created_at']) ? date('d M H:i', strtotime($al['created_at'])) : '-' ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="text-muted small mb-3">Belum ada riwayat access log.</p>
            <?php endif; ?>

            <!-- Notifikasi Sistem -->
            <div class="sa-section-label"><i class="bi bi-bell"></i>Notifikasi Sistem Terbaru</div>
            <?php if (!empty($recent_system_notifications)) : ?>
                <div class="d-flex flex-column gap-2">
                    <?php foreach (array_slice($recent_system_notifications, 0, 3) as $rn) : ?>
                        <div class="sa-log-item">
                            <div class="overflow-hidden flex-grow-1">
                                <div class="fw-semibold text-dark text-truncate" style="font-size:12.5px;"><?= esc($rn['title'] ?? 'Notifikasi') ?></div>
                                <div class="text-secondary text-truncate" style="font-size:11px;">
                                    Ke: <strong><?= esc($rn['penerima_nama'] ?? '-') ?></strong>
                                    (<?= esc(str_replace('_', ' ', $rn['penerima_role'] ?? '-')) ?>)
                                </div>
                            </div>
                            <span class="sa-log-badge flex-shrink-0 <?= !empty($rn['is_read']) ? 'sa-log-logout' : '' ?>" style="<?= empty($rn['is_read']) ? 'background:#EEF5FE; color:#1E60EC;' : '' ?>">
                                <?= !empty($rn['is_read']) ? 'Dibaca' : 'Baru' ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="text-muted small">Belum ada notifikasi sistem.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- =====================================================
     5. JEJAK AUDIT AKTIVITAS SISTEM (ACTIVITY LOG)
     ===================================================== -->
<div class="sa-panel mb-4">
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
        <div>
            <p class="sa-panel-title"><i class="bi bi-clock-history text-primary me-1"></i>Jejak Audit Aktivitas Sistem Terbaru</p>
            <p class="sa-panel-desc">Rekaman perubahan data, pembuatan akun, switch role, dan pengaturan sistem.</p>
        </div>
        <a href="<?= base_url('/audit-log/activity') ?>"
           class="btn btn-sm btn-primary rounded-3 fw-semibold flex-shrink-0"
           style="background-color:#1E60EC; border-color:#1E60EC; font-size:12px;">
            Buka Seluruh Activity Log
        </a>
    </div>

    <?php if (!empty($recent_activities)) : ?>
        <div class="row g-3">
            <?php foreach (array_slice($recent_activities, 0, 6) as $act) : ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="sa-activity-item">
                        <div class="sa-activity-icon <?= esc($act['color_class']) ?>">
                            <i class="bi <?= esc($act['icon']) ?>"></i>
                        </div>
                        <div class="overflow-hidden flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                <span class="badge-status <?= esc($act['badge_class']) ?>"><?= esc($act['badge']) ?></span>
                                <span class="text-muted" style="font-size:11px;"><?= esc($act['date']) ?> <?= esc($act['time']) ?></span>
                            </div>
                            <div class="fw-semibold text-dark text-truncate" style="font-size:12.5px;" title="<?= esc($act['title']) ?>"><?= esc($act['title']) ?></div>
                            <div class="text-secondary" style="font-size:11.5px; text-align: justify;">Oleh: <?= esc($act['user']) ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="text-center py-5 text-secondary small">
            <i class="bi bi-clock-history fs-2 d-block mb-2 opacity-25"></i>
            Belum ada rekaman aktivitas sistem.
        </div>
    <?php endif; ?>
</div>

<!-- =====================================================
     CHART.JS SCRIPTS
     ===================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    const trendData = <?= json_encode($super_trend_chart ?? ['labels' => [], 'activity' => [], 'login_ok' => [], 'login_failed' => []]) ?>;
    const roleData  = <?= json_encode($super_role_chart  ?? ['labels' => [], 'values'   => []]) ?>;

    // ---- Trend Chart ----
    const trendCanvas = document.getElementById('saTrendChart');
    if (trendCanvas) {
        new Chart(trendCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [
                    {
                        label: 'Transaksi Sistem',
                        data: trendData.activity,
                        borderColor: '#1E60EC',
                        backgroundColor: 'rgba(30,96,236,0.08)',
                        borderWidth: 2,
                        pointBackgroundColor: '#1E60EC',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'Login Sukses',
                        data: trendData.login_ok,
                        borderColor: '#16A36A',
                        backgroundColor: 'rgba(22,163,106,0.07)',
                        borderWidth: 2,
                        pointBackgroundColor: '#16A36A',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'Gagal Login',
                        data: trendData.login_failed,
                        borderColor: '#DC4444',
                        backgroundColor: 'rgba(220,68,68,0.07)',
                        borderWidth: 2,
                        pointBackgroundColor: '#DC4444',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 9,
                            padding: 16,
                            font: { family: 'Poppins', size: 11.5 },
                            color: '#64748B'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { family: 'Poppins', size: 11 }, color: '#94A3B8' },
                        grid: { color: '#F1F5F9' },
                        border: { color: '#F1F5F9' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Poppins', size: 11 }, color: '#94A3B8' },
                        border: { color: '#E5EAF1' }
                    }
                }
            }
        });
    }

    // ---- Role Doughnut Chart ----
    const roleCanvas = document.getElementById('saRoleChart');
    if (roleCanvas) {
        new Chart(roleCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: roleData.labels,
                datasets: [{
                    data: roleData.values,
                    backgroundColor: ['#7C3AED','#1E60EC','#0D9488','#3B82F6','#E9A23B','#16A36A'],
                    borderWidth: 2,
                    borderColor: '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '64%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 10,
                            padding: 12,
                            font: { family: 'Poppins', size: 11 },
                            color: '#64748B'
                        }
                    }
                }
            }
        });
    }

    // Init Bootstrap Tooltips
    const tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipEls.forEach(el => new bootstrap.Tooltip(el, { trigger: 'hover' }));
});
</script>
