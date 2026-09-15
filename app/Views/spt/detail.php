<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Detail SPT - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Detail Surat Perintah Tugas (SPT)</h2>

<div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; background-color: #f9f9f9;">
    <p><strong>Nomor SPT:</strong> <?= esc($spt['nomor_spt']) ?></p>
    <p><strong>Tanggal SPT:</strong> <?= esc($spt['tanggal_spt']) ?></p>
    <p><strong>Status:</strong> <?= esc($spt['status']) ?></p>
    <p><strong>Periode Pelaksanaan:</strong> <?= esc($spt['tanggal_mulai']) ?> s/d <?= esc($spt['tanggal_selesai']) ?></p>
    
    <hr>
    <p><strong>Perihal Penugasan:</strong></p>
    <div style="padding: 10px; background-color: #fff; border: 1px dashed #999;">
        <?= nl2br(esc($spt['perihal'])) ?>
    </div>
</div>

<h3>Daftar Pelaksana Tugas (Arsiparis):</h3>
<?php if (!empty($pelaksana_list)) : ?>
    <ul>
        <?php foreach ($pelaksana_list as $p) : ?>
            <li><?= esc($p['nama']) ?> (<?= esc($p['email']) ?>)</li>
        <?php endforeach; ?>
    </ul>
<?php else : ?>
    <p>Tidak ada data pelaksana yang terdaftar pada SPT ini.</p>
<?php endif; ?>

<div style="margin-top: 20px;">
    <a href="<?= base_url('/spt') ?>"><button type="button">Kembali ke Daftar SPT</button></a>
</div>

<?= $this->endSection() ?>
