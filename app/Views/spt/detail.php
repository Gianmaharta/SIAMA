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
    <div style="padding: 10px; background-color: #fff; border: 1px dashed #999; margin-bottom: 15px;">
        <?= nl2br(esc($spt['perihal'])) ?>
    </div>

    <?php if (!empty($spt['file_spt'])) : ?>
        <p><strong>Dokumen Fisik SPT:</strong></p>
        <a href="<?= base_url('uploads/spt/' . $spt['file_spt']) ?>" target="_blank" style="display: inline-block; background-color: #e74c3c; color: white; padding: 10px 20px; text-decoration: none; font-weight: bold; border-radius: 4px;">
            📄 Lihat Dokumen SPT (PDF)
        </a>
    <?php endif; ?>
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
    <?php $nama_role = session()->get('nama_role'); ?>
    <?php if (in_array($nama_role, ['Kepala_Bidang', 'Admin_OPD']) && $spt['status_penugasan'] === 'belum_ditugaskan') : ?>
        <a href="<?= base_url('/spt/assign/' . $spt['id_spt']) ?>"><button type="button" style="background-color: #3498db; color: white; border: none; padding: 8px 15px; cursor: pointer; margin-right: 10px;">Tugaskan Arsiparis</button></a>
    <?php endif; ?>
    <a href="<?= base_url('/spt') ?>"><button type="button" style="padding: 8px 15px;">Kembali ke Daftar SPT</button></a>
</div>

<?= $this->endSection() ?>
