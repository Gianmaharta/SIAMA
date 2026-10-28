<?php
$uri = service('uri');
$currentSegment = $uri->getSegment(1) ?? 'dashboard';
?>
<!-- SIDEBAR ADMINISTRATOR OPD -->
<aside class="sidebar" id="appSidebar">
    <!-- Brand / Logo SIAMA -->
    <a href="<?= base_url('/dashboard') ?>" class="sidebar-brand">
        <?php if (file_exists(FCPATH . 'logo siama1.png')) : ?>
            <img src="<?= base_url('logo siama1.png') ?>" alt="Logo SIAMA" class="sidebar-logo">
        <?php elseif (file_exists(FCPATH . 'Images/logo siama1.png')) : ?>
            <img src="<?= base_url('Images/logo siama1.png') ?>" alt="Logo SIAMA" class="sidebar-logo">
        <?php else: ?>
            <span class="fs-4 fw-bold text-white">SIAMA</span>
        <?php endif; ?>
    </a>

    <!-- Menu Khusus Administrator OPD -->
    <ul class="sidebar-menu">
        <!-- 1. DASHBOARD -->
        <li class="menu-group-label">DASHBOARD</li>
        <li class="nav-item">
            <a href="<?= base_url('/dashboard') ?>" class="nav-link <?= ($currentSegment === '' || $currentSegment === 'dashboard') ? 'active' : '' ?>">
                <i class="bi bi-house"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- 2. MANAJEMEN -->
        <li class="menu-group-label">MANAJEMEN</li>
        <li class="nav-item">
            <a href="<?= base_url('/users') ?>" class="nav-link <?= ($currentSegment === 'users') ? 'active' : '' ?>">
                <i class="bi bi-person"></i>
                <span>Manajemen Akun</span>
            </a>
        </li>

        <!-- 3. DATA MASTER -->
        <li class="menu-group-label">DATA MASTER</li>
        <li class="nav-item">
            <a href="<?= base_url('/bidang') ?>" class="nav-link <?= ($currentSegment === 'bidang') ? 'active' : '' ?>">
                <i class="bi bi-layers"></i>
                <span>Master Bidang</span>
            </a>
        </li>

        <!-- 4. PELAKSANAAN ALIH MEDIA -->
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
        <li class="nav-item">
            <a href="<?= base_url('/berita-acara') ?>" class="nav-link <?= ($currentSegment === 'berita-acara') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-check"></i>
                <span>Berita Acara</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/jra') ?>" class="nav-link <?= ($currentSegment === 'jra') ? 'active' : '' ?>">
                <i class="bi bi-folder"></i>
                <span>Master JRA</span>
            </a>
        </li>
    </ul>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
