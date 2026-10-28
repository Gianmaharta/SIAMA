<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Bidang - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/bidang-master.css?v=' . (file_exists(FCPATH . 'css/bidang-master.css') ? filemtime(FCPATH . 'css/bidang-master.css') : time())) ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header Halaman: Tombol Back Sejajar Judul -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('/bidang') ?>" class="btn-back-title" title="Kembali ke Master Bidang" aria-label="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="bidang-page-header mb-0">
        <h1 class="bidang-page-title mb-0">Edit Data Bidang</h1>
        <p class="bidang-page-subtitle mb-0">Perbarui data bidang untuk Organisasi Perangkat Daerah (OPD).</p>
    </div>
</div>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
        <div class="fw-medium small"><?= session()->getFlashdata('error') ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-12 col-lg-7 col-xl-6">
        <div class="bidang-table-card p-4">
            <form action="<?= base_url('/bidang/update/' . $bidang['id_bidang']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="bidang-form-label text-muted">ID Bidang (UUID)</label>
                    <input 
                        type="text" 
                        class="bidang-form-control bg-light font-monospace text-muted" 
                        value="<?= esc($bidang['id_bidang']) ?>" 
                        readonly 
                        disabled
                        tabindex="-1"
                        style="cursor: not-allowed;"
                    >
                </div>

                <div class="mb-3">
                    <label for="id_opd" class="bidang-form-label">
                        Organisasi Perangkat Daerah (OPD) <span class="text-danger">*</span>
                    </label>
                    <select name="id_opd" id="id_opd" class="bidang-form-control bidang-form-select" required <?= $id_opd_locked ? 'disabled' : '' ?>>
                        <option value="">-- Pilih Organisasi Perangkat Daerah --</option>
                        <?php foreach ($opds as $opd) : ?>
                            <option value="<?= esc($opd['id_opd']) ?>" <?= ($bidang['id_opd'] == $opd['id_opd'] || $id_opd_locked == $opd['id_opd']) ? 'selected' : '' ?>>
                                <?= esc($opd['nama_opd']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($id_opd_locked) : ?>
                        <input type="hidden" name="id_opd" value="<?= esc($id_opd_locked) ?>">
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label for="nama_bidang" class="bidang-form-label">
                        Nama Bidang <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama_bidang" 
                        id="nama_bidang" 
                        class="bidang-form-control" 
                        value="<?= esc($bidang['nama_bidang']) ?>" 
                        required 
                        autocomplete="off"
                    >
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                    <a href="<?= base_url('/bidang') ?>" class="bidang-btn-cancel">Batal</a>
                    <button type="submit" class="bidang-btn-add px-4">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
