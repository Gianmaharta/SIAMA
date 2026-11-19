<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Terbitkan SPT Baru - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Header Halaman: Tombol Back Sejajar Judul -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('/spt') ?>" class="btn-back-title" title="Kembali ke Daftar SPT" aria-label="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="spt-page-title mb-0" style="font-size: 24px; font-weight: 700; color: #172033;">Terbitkan Surat Perintah Tugas (SPT)</h1>
    </div>
</div>

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

    <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top mt-4">
        <a href="<?= base_url('/spt') ?>" class="btn btn-light border px-4 py-2 rounded-3 text-secondary fw-medium" style="font-size: 13.5px;">Batal</a>
        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-medium" style="background-color: #1E60EC; border-color: #1E60EC; font-size: 13.5px;">Terbitkan SPT</button>
    </div>
</form>
<?= $this->endSection() ?>
