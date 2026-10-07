<?php
$uri = service('uri');
$currentSegment = $uri->getSegment(1) ?? 'dashboard';
$role = session()->get('nama_role');

$notifModel = new \App\Models\NotificationModel();
$unread = 0;
if (session()->get('user_id')) {
    $unread = $notifModel->getUnreadCount(session()->get('user_id'));
}
?>
<aside class="sidebar">
    <a href="<?= base_url('/dashboard') ?>" class="sidebar-brand">
        <?php if (file_exists(FCPATH . 'Images/logo siama1.png')) : ?>
            <img src="<?= base_url('logo siama1.png') ?>" alt="Logo SIAMA" class="sidebar-logo">
        <?php else: ?>
            <span class="fs-4 fw-bold text-white">SIAMA</span>
        <?php endif; ?>
    </a>

    <ul class="sidebar-menu">
        <li class="menu-group-label">DASHBOARD</li>
        <li class="nav-item">
            <a href="<?= base_url('/dashboard') ?>" class="nav-link <?= ($currentSegment === '' || $currentSegment === 'dashboard') ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <?php if (in_array($role, ['Admin_Pemkab', 'Admin_OPD'])) : ?>
            <li class="menu-group-label">MANAJEMEN</li>
            <li class="nav-item">
                <a href="<?= base_url('/users') ?>" class="nav-link <?= ($currentSegment === 'users') ? 'active' : '' ?>">
                    <i class="bi bi-people-fill"></i>
                    <span>Manajemen Akun</span>
                </a>
            </li>
        <?php endif; ?>

        <li class="menu-group-label">DATA MASTER</li>
        <li class="nav-item">
            <a href="<?= base_url('/opd') ?>" class="nav-link <?= ($currentSegment === 'opd') ? 'active' : '' ?>">
                <i class="bi bi-bank2"></i>
                <span>Master OPD</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/bidang') ?>" class="nav-link <?= ($currentSegment === 'bidang') ? 'active' : '' ?>">
                <i class="bi bi-diagram-3-fill"></i>
                <span>Master Bidang</span>
            </a>
        </li>

        <li class="menu-group-label">PELAKSANAAN ALIH MEDIA</li>
        <li class="nav-item">
            <a href="<?= base_url('/spt') ?>" class="nav-link <?= ($currentSegment === 'spt') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-text-fill"></i>
                <span>Surat Perintah Tugas (SPT)</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/arsip') ?>" class="nav-link <?= ($currentSegment === 'arsip') ? 'active' : '' ?>">
                <i class="bi bi-folder2-open"></i>
                <span>Pengelolaan Arsip</span>
            </a>
        </li>
        <?php if ($role === 'Admin_Pemkab') : ?>
            <li class="nav-item">
                <a href="<?= base_url('/penilaian') ?>" class="nav-link <?= ($currentSegment === 'penilaian') ? 'active' : '' ?>">
                    <i class="bi bi-check2-square"></i>
                    <span>Penilaian Arsip</span>
                </a>
            </li>
        <?php endif; ?>
        <li class="nav-item">
            <a href="<?= base_url('/berita-acara') ?>" class="nav-link <?= ($currentSegment === 'berita-acara') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-check-fill"></i>
                <span>Berita Acara</span>
            </a>
        </li>

        <?php if ($role === 'Admin_Pemkab') : ?>
            <li class="menu-group-label">AUDIT LOG</li>
            <li class="nav-item">
                <a href="<?= base_url('/audit-log/access') ?>" class="nav-link <?= ($currentSegment === 'audit-log' && $uri->getSegment(2) === 'access') ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-lock-fill"></i>
                    <span>Access Log</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/audit-log/activity') ?>" class="nav-link <?= ($currentSegment === 'audit-log' && $uri->getSegment(2) === 'activity') ? 'active' : '' ?>">
                    <i class="bi bi-clock-history"></i>
                    <span>Activity Log</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</aside>
