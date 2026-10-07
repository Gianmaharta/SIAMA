<?php
$userName = session()->get('nama_lengkap') ?? 'Administrator';
$userRole = session()->get('nama_role') ?? 'Admin_Pemkab';
$userInitial = strtoupper(substr($userName, 0, 1));

$notifModel = new \App\Models\NotificationModel();
$unread = 0;
if (session()->get('user_id')) {
    $unread = $notifModel->getUnreadCount(session()->get('user_id'));
}
?>

<?php if (session()->has('original_admin_id')) : ?>
    <div class="impersonation-bar">
        <span>
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <strong>Mode Penyamaran Aktif:</strong> Anda sedang menyamar sebagai 
            <strong><?= esc($userName) ?></strong> (<?= esc($userRole) ?> - <?= esc(session()->get('impersonated_opd_nama') ?? 'OPD') ?>)
        </span>
        <a href="<?= base_url('/users/switch-back') ?>" class="impersonation-btn">
            <i class="bi bi-arrow-return-left me-1"></i> Kembali ke Admin Pemkab
        </a>
    </div>
<?php endif; ?>

<header class="top-navbar">
    <!-- Left: Search Box -->
    <div class="search-box">
        <i class="bi bi-search search-icon"></i>
        <input 
            type="text" 
            class="search-input" 
            placeholder="Cari menu, arsip, OPD, atau kata kunci..."
            aria-label="Cari"
        >
    </div>

    <!-- Right: User Area -->
    <div class="nav-user-area">
        <!-- Notification Button -->
        <a href="<?= base_url('/notification') ?>" class="nav-notif-btn" title="Notifikasi">
            <i class="bi bi-bell"></i>
            <?php if ($unread > 0) : ?>
                <span class="notif-dot"></span>
            <?php endif; ?>
        </a>

        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <div class="user-profile" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="avatar-circle">
                    <?= $userInitial ?>
                </div>
                <div class="user-info d-none d-sm-flex">
                    <span class="user-name"><?= esc($userName) ?></span>
                    <span class="user-role"><?= esc($userRole) ?></span>
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
                    <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2 text-danger" href="<?= base_url('/logout') ?>">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
