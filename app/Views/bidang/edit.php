<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Bidang - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Edit Data Bidang</h2>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('/bidang/update/' . $bidang['id_bidang']) ?>" method="POST">
    <?= csrf_field() ?>

    <div style="margin-bottom: 10px;">
        <label for="id_opd">Pilih OPD:</label><br>
        <select name="id_opd" id="id_opd" required <?= $id_opd_locked ? 'disabled' : '' ?>>
            <option value="">-- Pilih OPD --</option>
            <?php foreach ($opds as $opd) : ?>
                <option value="<?= $opd['id_opd'] ?>" <?= ($bidang['id_opd'] == $opd['id_opd'] || $id_opd_locked == $opd['id_opd']) ? 'selected' : '' ?>>
                    <?= esc($opd['nama_opd']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <?php if ($id_opd_locked) : ?>
            <input type="hidden" name="id_opd" value="<?= $id_opd_locked ?>">
        <?php endif; ?>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="nama_bidang">Nama Bidang:</label><br>
        <input type="text" name="nama_bidang" id="nama_bidang" size="50" value="<?= esc($bidang['nama_bidang']) ?>" required>
    </div>

    <button type="submit">Update</button>
    <a href="<?= base_url('/bidang') ?>"><button type="button">Batal</button></a>
</form>
<?= $this->endSection() ?>
