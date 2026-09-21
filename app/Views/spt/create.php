<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Terbitkan SPT Baru - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Terbitkan Surat Perintah Tugas (SPT)</h2>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('/spt/store') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div style="margin-bottom: 10px;">
        <label for="nomor_spt">Nomor SPT:</label><br>
        <input type="text" name="nomor_spt" id="nomor_spt" required>
    </div>

    <div style="margin-bottom: 10px;">
        <label for="tanggal_spt">Tanggal Terbit SPT:</label><br>
        <input type="date" name="tanggal_spt" id="tanggal_spt" required>
    </div>

    <div style="margin-bottom: 10px;">
        <label for="perihal">Perihal / Keterangan Penugasan:</label><br>
        <textarea name="perihal" id="perihal" rows="4" cols="50" required></textarea>
    </div>

    <div style="margin-bottom: 10px;">
        <label for="tanggal_mulai">Tanggal Mulai Pelaksanaan:</label><br>
        <input type="date" name="tanggal_mulai" id="tanggal_mulai" required>
    </div>

    <div style="margin-bottom: 10px;">
        <label for="tanggal_selesai">Tanggal Selesai Pelaksanaan:</label><br>
        <input type="date" name="tanggal_selesai" id="tanggal_selesai" required>
    </div>

    <div style="margin-bottom: 20px; padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd;">
        <label for="file_spt"><strong>Unggah Dokumen SPT (PDF):</strong> <span style="color:red">*</span></label><br>
        <input type="file" name="file_spt" id="file_spt" accept=".pdf" required style="margin-top: 5px;">
        <br><small style="color: #666;">Format yang didukung hanya PDF. Maksimal 5MB.</small>
    </div>

    <button type="submit">Terbitkan SPT</button>
    <a href="<?= base_url('/spt') ?>"><button type="button">Batal</button></a>
</form>
<?= $this->endSection() ?>
