<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Dashboard - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Dashboard SIAMA</h2>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')) : ?>
    <div style="color: green; border: 1px solid green; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; background-color: #f9f9f9;">
    <h3>Selamat Datang, <?= esc($nama_lengkap) ?>!</h3>
    <p>Ini adalah ringkasan statistik berdasarkan akses Anda sebagai <strong><?= esc($nama_role) ?></strong>.</p>
</div>

<h3>Statistik Ringkasan:</h3>
<ul>
    <?php if ($nama_role == 'Admin_Pemkab') : ?>
        <li>Total OPD: <strong><?= esc($stats['total_opd']) ?></strong></li>
        <li>Total Seluruh Pengguna: <strong><?= esc($stats['total_users']) ?></strong></li>
        <li>Total Arsip Global: <strong><?= esc($stats['total_arsip']) ?></strong></li>
        <li>Total SPT Global: <strong><?= esc($stats['total_spt']) ?></strong></li>
        <li>Total Berita Acara Global: <strong><?= esc($stats['total_berita_acara']) ?></strong></li>
    <?php elseif ($nama_role == 'Pimpinan') : ?>
        <li>Total SPT Diterbitkan: <strong><?= esc($stats['spt_diterbitkan']) ?></strong></li>
        <li>Berita Acara Menanti TTD Pimpinan: <strong><?= esc($stats['ba_menanti_pimpinan']) ?></strong></li>
    <?php elseif ($nama_role == 'Admin_OPD') : ?>
        <li>Total Pengguna di OPD Anda: <strong><?= esc($stats['total_users_opd']) ?></strong></li>
        <li>Total Bidang di OPD Anda: <strong><?= esc($stats['total_bidang_opd']) ?></strong></li>
        <li>Total Arsip di OPD Anda: <strong><?= esc($stats['total_arsip_opd']) ?></strong></li>
    <?php elseif ($nama_role == 'Kepala_Bidang') : ?>
        <li>Total Arsip di Bidang Anda: <strong><?= esc($stats['total_arsip_bidang']) ?></strong></li>
        <li>Berita Acara Menanti Verifikasi Kabid: <strong><?= esc($stats['ba_menanti_kabid']) ?></strong></li>
    <?php elseif ($nama_role == 'Arsiparis') : ?>
        <li>Total Arsip yang Anda Unggah: <strong><?= esc($stats['arsip_diunggah']) ?></strong></li>
        <li>Penugasan SPT Anda: <strong><?= esc($stats['penugasan_spt']) ?></strong></li>
    <?php endif; ?>
</ul>

<?= $this->endSection() ?>
