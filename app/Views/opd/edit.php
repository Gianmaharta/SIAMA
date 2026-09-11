<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit OPD - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Edit Data OPD</h2>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('/opd/update/' . $opd['id_opd']) ?>" method="POST">
    <?= csrf_field() ?>

    <div style="margin-bottom: 10px;">
        <label for="kode_opd">Kode OPD:</label><br>
        <input type="text" name="kode_opd" id="kode_opd" value="<?= esc($opd['kode_opd']) ?>" required>
    </div>

    <div style="margin-bottom: 10px;">
        <label for="nama_opd">Nama OPD:</label><br>
        <input type="text" name="nama_opd" id="nama_opd" size="50" value="<?= esc($opd['nama_opd']) ?>" required>
    </div>

    <div style="margin-bottom: 10px;">
        <label for="kuota_storage_mb">Kuota Storage (MB):</label><br>
        <input type="number" name="kuota_storage_mb" id="kuota_storage_mb" value="<?= esc($opd['kuota_storage_mb']) ?>" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="is_active">Status Aktif:</label><br>
        <select name="is_active" id="is_active">
            <option value="1" <?= $opd['is_active'] ? 'selected' : '' ?>>Aktif</option>
            <option value="0" <?= !$opd['is_active'] ? 'selected' : '' ?>>Non-Aktif</option>
        </select>
    </div>

    <button type="submit">Update</button>
    <a href="<?= base_url('/opd') ?>"><button type="button">Batal</button></a>
</form>
<?= $this->endSection() ?>
