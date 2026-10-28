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
<aside class="sidebar" id="appSidebar">
    <a href="<?= base_url('/dashboard') ?>" class="sidebar-brand">
        <?php if (file_exists(FCPATH . 'logo siama1.png')) : ?>
            <img src="<?= base_url('logo siama1.png') ?>" alt="Logo SIAMA" class="sidebar-logo">
        <?php elseif (file_exists(FCPATH . 'Images/logo siama1.png')) : ?>
            <img src="<?= base_url('Images/logo siama1.png') ?>" alt="Logo SIAMA" class="sidebar-logo">
        <?php else: ?>
            <span class="fs-4 fw-bold text-white">SIAMA</span>
        <?php endif; ?>
    </a>

    <?php if (in_array($role, ['Pimpinan', 'Kepala_Dinas', 'Kepala Dinas', 'Kepala_Bidang', 'Kepala Bidang', 'Kabid', 'Arsiparis'])) : ?>
        <!-- Menu Khusus Pimpinan / Kepala Bidang / Arsiparis -->
        <ul class="sidebar-menu sidebar-menu-pimpinan">
            <li class="nav-item">
                <a href="<?= base_url('/dashboard') ?>" class="nav-link <?= ($currentSegment === '' || $currentSegment === 'dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-house"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/spt') ?>" class="nav-link <?= ($currentSegment === 'spt') ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Surat Perintah Tugas (SPT)</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/arsip') ?>" class="nav-link <?= ($currentSegment === 'arsip') ? 'active' : '' ?>">
                    <i class="bi bi-folder"></i>
                    <span>Pengelolaan Arsip</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/berita-acara') ?>" class="nav-link <?= ($currentSegment === 'berita-acara') ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-check"></i>
                    <span>Berita Acara</span>
                </a>
            </li>
        </ul>
    <?php else : ?>
        <ul class="sidebar-menu">
            <li class="menu-group-label">DASHBOARD</li>
            <li class="nav-item">
                <a href="<?= base_url('/dashboard') ?>" class="nav-link <?= ($currentSegment === '' || $currentSegment === 'dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-house"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <?php if (in_array($role, ['Admin_Pemkab', 'Admin_OPD', 'Administrator OPD'])) : ?>
                <li class="menu-group-label">MANAJEMEN</li>
                <li class="nav-item">
                    <a href="<?= base_url('/users') ?>" class="nav-link <?= ($currentSegment === 'users') ? 'active' : '' ?>">
                        <i class="bi bi-person"></i>
                        <span>Manajemen Akun</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($role === 'Admin_Pemkab' || in_array($role, ['Admin_OPD', 'Administrator OPD'])) : ?>
                <li class="menu-group-label">DATA MASTER</li>
                <?php if ($role === 'Admin_Pemkab') : ?>
                    <li class="nav-item">
                        <a href="<?= base_url('/opd') ?>" class="nav-link <?= ($currentSegment === 'opd') ? 'active' : '' ?>">
                            <i class="bi bi-bank"></i>
                            <span>Master OPD</span>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a href="<?= base_url('/bidang') ?>" class="nav-link <?= ($currentSegment === 'bidang') ? 'active' : '' ?>">
                        <i class="bi bi-layers"></i>
                        <span>Master Bidang</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="menu-group-label">PELAKSANAAN ALIH MEDIA</li>
            <li class="nav-item">
                <a href="<?= base_url('/spt') ?>" class="nav-link <?= ($currentSegment === 'spt') ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Surat Perintah Tugas (SPT)</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/arsip') ?>" class="nav-link <?= ($currentSegment === 'arsip') ? 'active' : '' ?>">
                    <i class="bi bi-folder"></i>
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
                    <i class="bi bi-file-earmark-check"></i>
                    <span>Berita Acara</span>
                </a>
            </li>
            <?php if (in_array($role, ['Admin_OPD', 'Administrator OPD'])) : ?>
                <li class="nav-item">
                    <a href="<?= base_url('/jra') ?>" class="nav-link <?= ($currentSegment === 'jra') ? 'active' : '' ?>">
                        <i class="bi bi-folder"></i>
                        <span>Master JRA</span>
                    </a>
                </li>
            <?php endif; ?>

        <?php if ($role === 'Admin_Pemkab') : ?>
            <li class="menu-group-label">AUDIT LOG</li>
            <li class="nav-item">
                <a href="<?= base_url('/audit-log/access') ?>" class="nav-link <?= ($currentSegment === 'audit-log' && $uri->getSegment(2) === 'access') ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-lock"></i>
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
    <?php endif; ?>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

