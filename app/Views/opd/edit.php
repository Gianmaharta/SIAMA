<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Data OPD - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/opd-master.css?v=' . (file_exists(FCPATH . 'css/opd-master.css') ? filemtime(FCPATH . 'css/opd-master.css') : time())) ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Flash Message: Error -->
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
        <div class="fw-medium small">
            <?= session()->getFlashdata('error') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Header Halaman: Tombol Back Sejajar Judul -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('/opd') ?>" class="btn-back-title" title="Kembali ke Daftar OPD" aria-label="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="opd-page-title mb-0">Edit Data OPD</h1>
        <p class="opd-page-subtitle mb-0">Perbarui data instansi OPD <strong><?= esc($opd['nama_opd']) ?></strong>.</p>
    </div>
</div>

<!-- Card Form Edit OPD -->
<div class="row">
    <div class="col-12 col-lg-8">
        <div class="opd-table-card p-4">
            <form action="<?= base_url('/opd/update/' . $opd['id_opd']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="opd-form-label text-muted">ID OPD (UUID)</label>
                    <input 
                        type="text" 
                        class="opd-form-control bg-light font-monospace text-muted" 
                        value="<?= esc($opd['id_opd']) ?>" 
                        readonly
                        disabled
                        tabindex="-1"
                        style="cursor: not-allowed;"
                    >
                    <div class="opd-form-help">ID OPD bersifat permanen dari sistem dan tidak dapat diubah.</div>
                </div>

                <div class="mb-3">
                    <label for="kode_opd" class="opd-form-label text-muted">
                        Kode OPD <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="kode_opd" 
                        id="kode_opd" 
                        class="opd-form-control bg-light font-monospace text-muted" 
                        value="<?= old('kode_opd', esc($opd['kode_opd'])) ?>" 
                        readonly
                        tabindex="-1"
                        style="cursor: not-allowed;"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="nama_opd" class="opd-form-label">
                        Nama OPD <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama_opd" 
                        id="nama_opd" 
                        class="opd-form-control" 
                        value="<?= old('nama_opd', esc($opd['nama_opd'])) ?>" 
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="kuota_storage_mb" class="opd-form-label">
                        Kuota Storage (MB) <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="kuota_storage_mb" 
                        id="kuota_storage_mb" 
                        class="opd-form-control" 
                        value="<?= old('kuota_storage_mb', esc($opd['kuota_storage_mb'])) ?>" 
                        min="100" 
                        step="100" 
                        required
                    >
                </div>

                <div class="mb-4">
                    <label for="is_active" class="opd-form-label">Status Keaktifan</label>
                    <select name="is_active" id="is_active" class="opd-form-control opd-form-select">
                        <option value="1" <?= old('is_active', $opd['is_active']) == '1' ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= old('is_active', $opd['is_active']) == '0' ? 'selected' : '' ?>>Non Aktif</option>
                    </select>
                </div>

                <div class="d-flex align-items-center gap-2 pt-3 border-top">
                    <button type="submit" class="opd-btn-add px-4">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="<?= base_url('/opd') ?>" class="btn btn-light border px-4" style="height: 42px; border-radius: 8px; font-weight: 500; display: inline-flex; align-items: center;">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
