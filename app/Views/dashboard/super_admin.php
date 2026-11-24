<?php
$sysSettings = $system_settings ?? [];
$siteName    = $sysSettings['site_name'] ?? 'SIAMA';
$pwMode      = $sysSettings['password_mode'] ?? 'default';
$defaultPw   = $sysSettings['default_password'] ?? 'Admin123!';

$pwModeLabel = ($pwMode === 'random') ? 'Password Random Otomatis' : 'Password Default Website';
?>

<style>
.sa-hero-banner {
    background: linear-gradient(135deg, #0F172A 0%, #1E293B 55%, #1E3A8A 100%);
    border-radius: 18px;
    padding: 26px 28px;
    color: #FFFFFF;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.14);
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}
.sa-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(168, 85, 247, 0.22);
    border: 1px solid rgba(192, 132, 252, 0.45);
    color: #E9D5FF;
    font-size: 11.5px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 50px;
}
.sa-config-pill {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 12px;
    padding: 10px 14px;
    font-size: 12px;
}
.sa-metric-card {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 16px;
    padding: 20px;
    height: 100%;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
    transition: transform 0.18s ease, box-shadow 0.18s ease;
}
.sa-metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
}
.sa-metric-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 21px;
    flex-shrink: 0;
}
.sa-panel-card {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 16px;
    padding: 22px;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
}
.sa-quick-switch-row {
    border-bottom: 1px solid #F1F5F9;
    padding: 10px 0;
}
.sa-quick-switch-row:last-child {
    border-bottom: 0;
}
</style>

<!-- 1. HERO BANNER PUSAT KENDALI SUPER ADMIN -->
<div class="sa-hero-banner">
    <div class="row align-items-center g-4">
        <div class="col-12 col-xl-7">
            <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                <span class="sa-hero-badge">
                    <i class="bi bi-shield-fill-check"></i>
                    <span>SUPER ADMIN &bull; SYSTEM CONTROL CENTER</span>
                </span>
                <span class="badge bg-success bg-opacity-25 text-success-emphasis border border-success border-opacity-25 rounded-pill px-2.5 py-1" style="font-size: 11px; color: #86EFAC !important;">
                    <i class="bi bi-circle-fill me-1" style="font-size: 7px;"></i> Sistem &amp; Basis Data Aktif
                </span>
            </div>
            <h1 class="h3 fw-bold text-white mb-1" style="font-size: 24px; letter-spacing: -0.3px;">
                Pusat Kendali Sistem &amp; Keamanan <?= esc($siteName) ?>
            </h1>
            <p class="text-white-50 small mb-3" style="max-width: 650px; line-height: 1.6;">
                Selamat datang, <strong class="text-white"><?= esc($nama_lengkap ?? 'Super Administrator') ?></strong>. Kelola konfigurasi global website, kebijakan password default, fitur <em>Switch User</em> lintas peran, serta pantau log keamanan dan notifikasi sistem secara terpusat.
            </p>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="<?= base_url('/settings') ?>" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-2" style="background-color: #2563EB; border-color: #2563EB;">
                    <i class="bi bi-sliders"></i>
                    <span>Pengaturan Website &amp; Password</span>
                </a>
                <a href="<?= base_url('/users') ?>" class="btn btn-light btn-sm px-3 py-2 rounded-3 fw-semibold text-dark d-inline-flex align-items-center gap-2">
                    <i class="bi bi-person-badge"></i>
                    <span>Manajemen Akun &amp; Switch User</span>
                </a>
                <a href="<?= base_url('/swagger') ?>" class="btn btn-outline-light btn-sm px-3 py-2 rounded-3 fw-medium d-inline-flex align-items-center gap-2">
                    <i class="bi bi-braces-asterisk"></i>
                    <span>API Swagger</span>
                </a>
                <a href="<?= base_url('/') ?>" target="_blank" class="btn btn-outline-info btn-sm px-3 py-2 rounded-3 fw-medium d-inline-flex align-items-center gap-2">
                    <i class="bi bi-globe2"></i>
                    <span>Landing Page</span>
                </a>
            </div>
        </div>

        <!-- Ringkasan Konfigurasi Sistem Aktif (Password, SSO, Branding) -->
        <div class="col-12 col-xl-5">
            <div class="row g-2">
                <div class="col-12 col-sm-6">
                    <div class="sa-config-pill">
                        <div class="text-white-50 mb-1" style="font-size: 11px;"><i class="bi bi-key-fill text-warning me-1"></i>Password Default Sistem</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <code class="text-info fw-bold fs-6" id="saDashDefaultPw"><?= esc($defaultPw) ?></code>
                            <span class="badge bg-white bg-opacity-10 text-white" style="font-size: 10px;"><?= esc($pwModeLabel) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="sa-config-pill">
                        <div class="text-white-50 mb-1" style="font-size: 11px;"><i class="bi bi-folder-check text-info me-1"></i>Total Arsip &amp; Berita Acara</div>
                        <div class="fw-semibold text-white text-truncate">
                            <?= number_format($stats['total_arsip'] ?? 0, 0, ',', '.') ?> Arsip &bull; <?= number_format($stats['total_berita_acara'] ?? 0, 0, ',', '.') ?> BA
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="sa-config-pill">
                        <div class="text-white-50 mb-1" style="font-size: 11px;"><i class="bi bi-bell-fill text-success me-1"></i>Monitor Notifikasi Sistem</div>
                        <div class="fw-semibold text-white">
                            <?= number_format($stats['total_notifications'] ?? 0, 0, ',', '.') ?> Total
                            <span class="text-warning ms-1">(<?= number_format($stats['unread_notifications'] ?? 0, 0, ',', '.') ?> Belum Dibaca)</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="sa-config-pill">
                        <div class="text-white-50 mb-1" style="font-size: 11px;"><i class="bi bi-database-check text-primary me-1"></i>Data Master Kearsipan</div>
                        <div class="fw-semibold text-white">
                            <?= number_format($stats['total_opd'] ?? 0, 0, ',', '.') ?> OPD &bull; <?= number_format($stats['total_jra'] ?? 0, 0, ',', '.') ?> JRA
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. EMPAT KARTU METRIK UTAMA SUPER ADMIN (AKUN, PASSWORD, LOG KEAMANAN, JEJAK AUDIT) -->
<div class="row g-3 g-xl-4 mb-4">
    <!-- Card 1: Total Akun & Status Keaktifan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="sa-metric-card">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div>
                    <div class="text-secondary small fw-medium mb-1">Total Akun Terdaftar</div>
                    <div class="h3 fw-bold text-dark mb-0"><?= number_format($stats['total_users'] ?? 0, 0, ',', '.') ?></div>
                </div>
                <div class="sa-metric-icon" style="background-color: #EFF6FF; color: #1E60EC;">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between small pt-2 border-top">
                <span class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i><?= (int) ($stats['active_users'] ?? 0) ?> Aktif</span>
                <span class="text-danger fw-medium"><i class="bi bi-x-circle-fill me-1"></i><?= (int) ($stats['inactive_users'] ?? 0) ?> Nonaktif</span>
            </div>
        </div>
    </div>

    <!-- Card 2: Keamanan Password Default -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="sa-metric-card">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div>
                    <div class="text-secondary small fw-medium mb-1">Akun Password Default</div>
                    <div class="h3 fw-bold text-dark mb-0"><?= number_format($stats['default_pw_users'] ?? 0, 0, ',', '.') ?></div>
                </div>
                <div class="sa-metric-icon" style="background-color: #FEF3C7; color: #D97706;">
                    <i class="bi bi-key-fill"></i>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between small pt-2 border-top">
                <span class="text-secondary">Default: <code class="fw-bold text-dark"><?= esc($defaultPw) ?></code></span>
                <a href="<?= base_url('/settings') ?>" class="text-primary text-decoration-none fw-semibold">Ubah &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Card 3: Access Log (Login & Keamanan) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="sa-metric-card">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div>
                    <div class="text-secondary small fw-medium mb-1">Total Log Akses (Login)</div>
                    <div class="h3 fw-bold text-dark mb-0"><?= number_format($stats['total_access_logs'] ?? 0, 0, ',', '.') ?></div>
                </div>
                <div class="sa-metric-icon" style="background-color: #ECFDF5; color: #059669;">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between small pt-2 border-top">
                <span class="text-success fw-medium"><?= number_format($stats['login_success_count'] ?? 0, 0, ',', '.') ?> Login Sukses</span>
                <span class="text-danger fw-semibold"><?= number_format($stats['login_failed_count'] ?? 0, 0, ',', '.') ?> Gagal</span>
            </div>
        </div>
    </div>

    <!-- Card 4: Activity Log (Jejak Transaksi Sistem) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="sa-metric-card">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div>
                    <div class="text-secondary small fw-medium mb-1">Total Jejak Activity Log</div>
                    <div class="h3 fw-bold text-dark mb-0"><?= number_format($stats['total_activity_logs'] ?? 0, 0, ',', '.') ?></div>
                </div>
                <div class="sa-metric-icon" style="background-color: #F3E8FF; color: #7C3AED;">
                    <i class="bi bi-activity"></i>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between small pt-2 border-top">
                <span class="text-secondary"><strong class="text-dark"><?= number_format($stats['today_activity_logs'] ?? 0, 0, ',', '.') ?></strong> aksi hari ini</span>
                <a href="<?= base_url('/audit-log/activity') ?>" class="text-primary text-decoration-none fw-semibold">Detail &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- 3. GRAFIK VISUALISASI AKTIVITAS SISTEM & DISTRIBUSI ROLE HIERARKI -->
<div class="row g-4 mb-4">
    <!-- Grafik 1: Tren Aktivitas Sistem & Keamanan Login 7 Hari Terakhir -->
    <div class="col-12 col-xl-8">
        <div class="sa-panel-card h-100">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 16px;">
                        <i class="bi bi-graph-up-arrow text-primary me-1"></i>
                        Tren Aktivitas Sistem &amp; Keamanan Akses (7 Hari Terakhir)
                    </h5>
                    <p class="text-secondary small mb-0">Perbandingan volume transaksi pengguna (Activity Log), Login Berhasil, dan Percobaan Login Gagal.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="<?= base_url('/audit-log/activity') ?>" class="btn btn-light border btn-sm rounded-3 small fw-medium">Activity Log</a>
                    <a href="<?= base_url('/audit-log/access') ?>" class="btn btn-light border btn-sm rounded-3 small fw-medium">Access Log</a>
                </div>
            </div>
            <div style="height: 270px; position: relative;">
                <canvas id="saTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Grafik 2: Distribusi Akun Berdasarkan Hierarki Role -->
    <div class="col-12 col-xl-4">
        <div class="sa-panel-card h-100">
            <div class="mb-3">
                <h5 class="fw-bold text-dark mb-1" style="font-size: 16px;">
                    <i class="bi bi-diagram-3-fill text-primary me-1"></i>
                    Komposisi Hierarki Role Pengguna
                </h5>
                <p class="text-secondary small mb-0">Distribusi akun dari Super Admin hingga Arsiparis.</p>
            </div>
            <div style="height: 220px; position: relative;">
                <canvas id="saRoleChart"></canvas>
            </div>
            <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between small text-secondary">
                <span>Hierarki Tertinggi: <strong class="text-dark">Super Admin</strong></span>
                <a href="<?= base_url('/users') ?>" class="text-primary text-decoration-none fw-semibold">Kelola Role &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- 4. PANEL QUICK SWITCH USER (IMPERSONATION) & MONITOR LOG / NOTIFIKASI -->
<div class="row g-4 mb-4">
    <!-- Kolom Kiri: Panel Cepat Switch User (Khusus Super Admin) -->
    <div class="col-12 col-xl-6">
        <div class="sa-panel-card h-100">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 16px;">
                        <i class="bi bi-person-bounding-box text-primary me-1"></i>
                        Quick Switch User (Masuk Sebagai Peran Lain)
                    </h5>
                    <p class="text-secondary small mb-0">Hak eksklusif Super Admin untuk beralih sesi ke akun pengguna lain dalam 1 klik.</p>
                </div>
                <a href="<?= base_url('/users') ?>" class="btn btn-outline-primary btn-sm rounded-3 fw-medium">
                    Semua Akun (<?= (int) ($stats['total_users'] ?? 0) ?>)
                </a>
            </div>

            <?php if (!empty($quick_switch_users)) : ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size: 13px;">
                        <thead class="table-light">
                            <tr>
                                <th>Pengguna</th>
                                <th>Role</th>
                                <th>OPD</th>
                                <th class="text-end">Aksi Switch</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($quick_switch_users as $qu) : ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= esc($qu['nama']) ?></div>
                                        <div class="text-muted" style="font-size: 11.5px;"><?= esc($qu['email']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1" style="font-size: 11px;">
                                            <?= esc(str_replace('_', ' ', $qu['nama_role'] ?? '-')) ?>
                                        </span>
                                    </td>
                                    <td class="text-secondary small">
                                        <?= esc($qu['nama_opd'] ?? 'Tingkat Pemkab') ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= base_url('/users/switch/' . $qu['id_user']) ?>" class="btn btn-sm btn-primary px-2.5 py-1 rounded-2 fw-medium d-inline-flex align-items-center gap-1" style="background-color: #1E60EC; border-color: #1E60EC; font-size: 12px;" title="Masuk Sebagai <?= esc($qu['nama']) ?>">
                                            <i class="bi bi-box-arrow-in-right"></i>
                                            <span>Switch</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="text-center py-4 text-secondary small">Belum ada akun pengguna lain yang terdaftar.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Kolom Kanan: Monitor Notifikasi Sistem & Log Akses Keamanan Terbaru -->
    <div class="col-12 col-xl-6">
        <div class="sa-panel-card h-100">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 16px;">
                        <i class="bi bi-shield-exclamation text-primary me-1"></i>
                        Log Akses Login &amp; Notifikasi Sistem Terbaru
                    </h5>
                    <p class="text-secondary small mb-0">Pantauan real-time autentikasi pengguna dan distribusi notifikasi sistem.</p>
                </div>
                <a href="<?= base_url('/audit-log/access') ?>" class="btn btn-outline-secondary btn-sm rounded-3 fw-medium">
                    Lihat Access Log
                </a>
            </div>

            <!-- Sub-bagian 1: 4 Log Akses Terakhir -->
            <div class="mb-3">
                <div class="fw-semibold small text-uppercase text-secondary mb-2" style="font-size: 11px; letter-spacing: 0.4px;">
                    <i class="bi bi-key me-1"></i>Riwayat Autentikasi Terbaru (Access Log)
                </div>
                <?php if (!empty($recent_access_logs)) : ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach (array_slice($recent_access_logs, 0, 4) as $al) :
                            $st = strtolower((string) ($al['status'] ?? 'login'));
                            $isFail = ($st === 'failed_login');
                            $isLogout = ($st === 'logout');
                            $badgeCls = $isFail ? 'bg-danger-subtle text-danger' : ($isLogout ? 'bg-secondary-subtle text-secondary' : 'bg-success-subtle text-success');
                            $badgeTxt = $isFail ? 'Gagal Login' : ($isLogout ? 'Logout' : 'Login Sukses');
                        ?>
                            <div class="d-flex align-items-center justify-content-between p-2 px-3 rounded-3 bg-light border">
                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                    <span class="badge <?= $badgeCls ?> rounded-pill px-2 py-1" style="font-size: 10.5px;"><?= $badgeTxt ?></span>
                                    <div class="text-truncate small">
                                        <strong class="text-dark"><?= esc($al['nama_user'] ?? 'Unknown') ?></strong>
                                        <span class="text-muted">(<?= esc(str_replace('_', ' ', $al['nama_role'] ?? '-')) ?>)</span>
                                    </div>
                                </div>
                                <div class="text-muted small flex-shrink-0 ms-2" style="font-size: 11.5px;">
                                    <?= !empty($al['created_at']) ? date('d M H:i', strtotime($al['created_at'])) : '-' ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="text-muted small py-2">Belum ada riwayat access log.</div>
                <?php endif; ?>
            </div>

            <!-- Sub-bagian 2: Notifikasi Sistem Terbaru -->
            <div>
                <div class="fw-semibold small text-uppercase text-secondary mb-2" style="font-size: 11px; letter-spacing: 0.4px;">
                    <i class="bi bi-bell me-1"></i>Notifikasi Sistem Terkirim Terbaru
                </div>
                <?php if (!empty($recent_system_notifications)) : ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach (array_slice($recent_system_notifications, 0, 3) as $rn) : ?>
                            <div class="p-2 px-3 rounded-3 border bg-white d-flex align-items-start justify-content-between gap-2">
                                <div class="small overflow-hidden">
                                    <div class="fw-semibold text-dark text-truncate"><?= esc($rn['title'] ?? 'Notifikasi') ?></div>
                                    <div class="text-secondary text-truncate" style="font-size: 11.5px;">
                                        Ke: <strong><?= esc($rn['penerima_nama'] ?? '-') ?></strong> (<?= esc(str_replace('_', ' ', $rn['penerima_role'] ?? '-')) ?>)
                                    </div>
                                </div>
                                <span class="badge <?= !empty($rn['is_read']) ? 'bg-light text-muted border' : 'bg-primary-subtle text-primary' ?> rounded-pill" style="font-size: 10px;">
                                    <?= !empty($rn['is_read']) ? 'Dibaca' : 'Baru' ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="text-muted small py-2">Belum ada notifikasi sistem.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- 5. JEJAK AUDIT TRANSAKSI TERBARU (ACTIVITY LOG) -->
<div class="sa-panel-card mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-1" style="font-size: 16px;">
                <i class="bi bi-clock-history text-primary me-1"></i>
                Jejak Audit Aktivitas Sistem Terbaru (Activity Log)
            </h5>
            <p class="text-secondary small mb-0">Rekaman perubahan data, pembuatan akun, switch role, dan pengaturan sistem.</p>
        </div>
        <a href="<?= base_url('/audit-log/activity') ?>" class="btn btn-primary btn-sm px-3 py-1.5 rounded-3 fw-medium" style="background-color: #1E60EC; border-color: #1E60EC;">
            Buka Seluruh Activity Log
        </a>
    </div>

    <?php if (!empty($recent_activities)) : ?>
        <div class="row g-3">
            <?php foreach (array_slice($recent_activities, 0, 6) as $act) : ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="p-3 rounded-3 border bg-light h-100 d-flex align-items-start gap-3">
                        <div class="activity-icon <?= esc($act['color_class']) ?> flex-shrink-0">
                            <i class="bi <?= esc($act['icon']) ?>"></i>
                        </div>
                        <div class="overflow-hidden flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                <span class="badge-status <?= esc($act['badge_class']) ?>" style="font-size: 10.5px;"><?= esc($act['badge']) ?></span>
                                <span class="text-muted" style="font-size: 11px;"><?= esc($act['date']) ?> <?= esc($act['time']) ?></span>
                            </div>
                            <div class="fw-semibold text-dark small text-truncate" title="<?= esc($act['title']) ?>"><?= esc($act['title']) ?></div>
                            <div class="text-secondary" style="font-size: 11.5px;">Oleh: <?= esc($act['user']) ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="text-center py-4 text-secondary small">Belum ada rekaman aktivitas sistem.</div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    const trendData = <?= json_encode($super_trend_chart ?? ['labels' => [], 'activity' => [], 'login_ok' => [], 'login_failed' => []]) ?>;
    const roleData  = <?= json_encode($super_role_chart ?? ['labels' => [], 'values' => []]) ?>;

    const trendCanvas = document.getElementById('saTrendChart');
    if (trendCanvas) {
        new Chart(trendCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: trendData.labels,
                datasets: [
                    {
                        label: 'Transaksi Sistem (Activity Log)',
                        data: trendData.activity,
                        backgroundColor: 'rgba(30, 96, 236, 0.82)',
                        borderRadius: 6,
                        barPercentage: 0.65
                    },
                    {
                        label: 'Login Sukses',
                        data: trendData.login_ok,
                        backgroundColor: 'rgba(16, 185, 129, 0.82)',
                        borderRadius: 6,
                        barPercentage: 0.65
                    },
                    {
                        label: 'Gagal Login',
                        data: trendData.login_failed,
                        backgroundColor: 'rgba(239, 68, 68, 0.85)',
                        borderRadius: 6,
                        barPercentage: 0.65
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 12, font: { family: 'Poppins', size: 12 } } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    const roleCanvas = document.getElementById('saRoleChart');
    if (roleCanvas) {
        new Chart(roleCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: roleData.labels,
                datasets: [{
                    data: roleData.values,
                    backgroundColor: ['#7C3AED', '#1E60EC', '#0D9488', '#3B82F6', '#F59E0B', '#10B981'],
                    borderWidth: 2,
                    borderColor: '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 11, font: { family: 'Poppins', size: 11.5 } } }
                }
            }
        });
    }
});
</script>
