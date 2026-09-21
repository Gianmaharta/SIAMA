<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Tugaskan Arsiparis - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Tugaskan Arsiparis</h2>

<div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; background-color: #f9f9f9;">
    <p><strong>Nomor SPT:</strong> <?= esc($spt['nomor_spt']) ?></p>
    <p><strong>Tanggal SPT:</strong> <?= esc($spt['tanggal_spt']) ?></p>
    <p><strong>Periode:</strong> <?= esc($spt['tanggal_mulai']) ?> s/d <?= esc($spt['tanggal_selesai']) ?></p>
    <p><strong>Perihal:</strong> <?= esc($spt['perihal']) ?></p>
</div>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('/spt/process-assign/' . $spt['id_spt']) ?>" method="POST">
    <?= csrf_field() ?>

    <div style="margin-bottom: 15px;">
        <label for="id_user"><strong>Pilih Arsiparis:</strong></label><br>
        <select name="id_user" id="id_user" required style="padding: 5px; min-width: 300px; margin-top: 5px;">
            <option value="">-- Pilih Arsiparis --</option>
            <?php foreach ($arsiparis_list as $arsiparis) : ?>
                <option value="<?= esc($arsiparis['id_user']) ?>">
                    <?= esc($arsiparis['nama']) ?> (<?= esc($arsiparis['email']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" style="background-color: #3498db; color: white; padding: 8px 15px; border: none; cursor: pointer;">Tugaskan</button>
    <a href="<?= base_url('/spt') ?>"><button type="button" style="padding: 8px 15px;">Batal</button></a>
</form>

<?= $this->endSection() ?>
