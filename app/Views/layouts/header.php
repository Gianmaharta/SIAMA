<?php
$userName = session()->get('nama_lengkap') ?? 'Administrator';
$userRole = session()->get('nama_role') ?? 'Admin_Pemkab';
$opdId = session()->get('id_opd');
$userInitial = !empty($userName) ? strtoupper(mb_substr(trim($userName), 0, 1)) : 'U';

// Mapping nama role yang rapi dan profesional
$roleDisplayMap = [
    'Admin_Pemkab'      => 'Admin Pemkab',
    'Admin_OPD'         => 'Admin OPD',
    'Administrator OPD' => 'Admin OPD',
    'Pimpinan'          => 'Kepala Dinas',
    'Kepala_Dinas'      => 'Kepala Dinas',
    'Kepala Dinas'      => 'Kepala Dinas',
    'Kepala_Bidang'     => 'Kepala Bidang',
    'Kepala Bidang'     => 'Kepala Bidang',
    'Kabid'             => 'Kepala Bidang',
    'Arsiparis'         => 'Arsiparis',
];
$roleDisplay = $roleDisplayMap[$userRole] ?? str_replace('_', ' ', $userRole);

$notifModel = new \App\Models\NotificationModel();
$unread = 0;
if (session()->get('user_id')) {
    $unread = $notifModel->getUnreadCount(session()->get('user_id'));
}
?>

<?php if (session()->has('original_admin_id')) : ?>
    <?php
    $cleanName = trim($userName);
    if (preg_match('/\((.*?)\)$/', $cleanName, $matches)) {
        $inParen = trim($matches[1]);
        if (strcasecmp($inParen, $roleDisplay) === 0 
            || strcasecmp($inParen, $userRole) === 0 
            || in_array(strtolower($inParen), ['arsiparis', 'pimpinan', 'kepala dinas', 'kepala bidang', 'kabid', 'admin opd', 'admin pemkab'])) {
            $cleanName = trim(preg_replace('/\s*\([^)]*\)$/', '', $cleanName));
        }
    }
    $impersonatedUserTitle = 'Sedang Masuk sebagai ' . $cleanName . ' (' . $roleDisplay . ')';

    $impersonatedOpd = session()->get('impersonated_opd_nama');
    if (empty($impersonatedOpd) && session()->get('id_opd')) {
        $opdModel = new \App\Models\OpdModel();
        $opdData = $opdModel->find(session()->get('id_opd'));
        $impersonatedOpd = $opdData['nama_opd'] ?? '';
    }
    $impersonatedOpdFormatted = (!empty($impersonatedOpd) && $impersonatedOpd !== '-') 
        ? rtrim($impersonatedOpd, " .\t\n\r\0\x0B") . '.' 
        : '';
    ?>
    <div class="impersonation-bar">
        <div class="impersonation-content">
            <div class="impersonation-title">
                <i class="bi bi-info-circle me-2"></i><?= esc($impersonatedUserTitle) ?>
            </div>
            <?php if (!empty($impersonatedOpdFormatted)) : ?>
                <div class="impersonation-subtitle"><?= esc($impersonatedOpdFormatted) ?></div>
            <?php endif; ?>
        </div>
        <a href="<?= base_url('/users/switch-back') ?>" class="impersonation-btn">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Akun Admin Pemkab
        </a>
    </div>
<?php endif; ?>

<header class="top-navbar">
    <div class="d-flex align-items-center flex-grow-1 min-w-0 me-2" style="min-width: 0;">
        <!-- Mobile Sidebar Toggle -->
        <button class="btn btn-link text-secondary p-0 me-2 me-sm-3 d-lg-none border-0 shadow-none flex-shrink-0" id="sidebarToggleBtn" type="button" aria-label="Buka Menu Sidebar">
            <i class="bi bi-list fs-2" style="line-height: 1;"></i>
        </button>
        <!-- Left: Search Box -->
        <div class="search-box">
            <i class="bi bi-search search-icon"></i>
            <input 
                type="text" 
                class="search-input" 
                placeholder="Cari menu, arsip, OPD..." 
                aria-label="Cari"
            >
        </div>
    </div>

    <!-- Right: User Area -->
    <div class="nav-user-area">
        <!-- Notification Button (Langsung Menampilkan Halaman Semua Notifikasi) -->
        <a href="<?= base_url('/notification') ?>" class="nav-notif-btn" id="navNotifBtn" title="Notifikasi">
            <i class="bi bi-bell"></i>
            <span class="notif-badge-count <?= $unread > 0 ? '' : 'd-none' ?>" id="navNotifBadge">
                <?= $unread > 99 ? '99+' : $unread ?>
            </span>
        </a>

        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <div class="user-profile" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="avatar-circle">
                    <?= $userInitial ?>
                </div>
                <div class="user-info d-none d-sm-flex">
                    <span class="user-name"><?= esc($userName) ?></span>
                    <span class="user-role"><?= esc($roleDisplay) ?></span>
                </div>
                <i class="bi bi-chevron-down dropdown-arrow ms-1"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 py-2" style="border-radius: 12px; font-size: 13.5px;">
                <li class="px-3 py-1 text-muted small d-sm-none">
                    <strong><?= esc($userName) ?></strong><br>
                    <span><?= esc($userRole) ?></span>
                </li>
                <li class="d-sm-none"><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2" href="<?= base_url('/change-password') ?>">
                        <i class="bi bi-key text-muted"></i> Ganti Password
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a href="javascript:void(0)" class="dropdown-item py-2 px-3 d-flex align-items-center gap-2 text-danger" data-bs-toggle="modal" data-bs-target="#modalLogout" role="button">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>

