<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Tambah Master JRA - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/jra-master.css?v=' . (file_exists(FCPATH . 'css/jra-master.css') ? filemtime(FCPATH . 'css/jra-master.css') : time())) ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header Halaman: Tombol Back Sejajar Judul -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('/jra') ?>" class="btn-back-title" title="Kembali ke Master JRA" aria-label="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="jra-page-title mb-0">Tambah Master JRA</h1>
        <p class="jra-page-subtitle mb-0">Tambahkan aturan jadwal retensi arsip baru ke dalam sistem.</p>
    </div>
</div>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
        <div class="fw-medium small"><?= session()->getFlashdata('error') ?></div>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-12 col-xl-9">
        <div class="jra-table-card">
            <form action="<?= base_url('/jra/store') ?>" method="post">
                <?= csrf_field() ?>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="jra-form-label">Kode Klasifikasi <span class="text-danger">*</span></label>
                        <input type="text" name="kode" value="<?= old('kode') ?>" required class="jra-form-control" placeholder="Contoh: 000.9.1">
                    </div>
                    <div class="col-md-8">
                        <label class="jra-form-label">Uraian / Nama Klasifikasi <span class="text-danger">*</span></label>
                        <input type="text" name="nama_klasifikasi" value="<?= old('nama_klasifikasi') ?>" required class="jra-form-control" placeholder="Contoh: Teletest">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="jra-form-label">Deskripsi Klasifikasi (Opsional)</label>
                    <textarea name="deskripsi" class="jra-form-control" rows="2" placeholder="Uraian ringkas mengenai klasifikasi arsip..."><?= old('deskripsi') ?></textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="jra-form-label">Retensi Aktif (Tahun) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="retensi_aktif" value="<?= old('retensi_aktif', 2) ?>" min="0" required class="form-control jra-form-control">
                            <span class="input-group-text bg-light text-muted small">Tahun</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="jra-form-label">Retensi Inaktif (Tahun) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="retensi_inaktif" value="<?= old('retensi_inaktif', 1) ?>" min="0" required class="form-control jra-form-control">
                            <span class="input-group-text bg-light text-muted small">Tahun</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="jra-form-label">Keterangan Retensi <span class="text-danger">*</span></label>
                        <select name="keterangan_retensi" required class="form-select jra-form-control">
                            <option value="Musnah" <?= old('keterangan_retensi') == 'Musnah' ? 'selected' : '' ?>>Musnah</option>
                            <option value="Permanen" <?= old('keterangan_retensi') == 'Permanen' ? 'selected' : '' ?>>Permanen</option>
                            <option value="Dinilai Kembali" <?= old('keterangan_retensi') == 'Dinilai Kembali' ? 'selected' : '' ?>>Dinilai Kembali</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="jra-form-label">Keamanan <span class="text-danger">*</span></label>
                        <select name="klasifikasi_keamanan" required class="form-select jra-form-control">
                            <option value="Biasa" <?= old('klasifikasi_keamanan') == 'Biasa' ? 'selected' : '' ?>>Biasa</option>
                            <option value="Terbatas" <?= old('klasifikasi_keamanan') == 'Terbatas' ? 'selected' : '' ?>>Terbatas</option>
                            <option value="Rahasia" <?= old('klasifikasi_keamanan') == 'Rahasia' ? 'selected' : '' ?>>Rahasia</option>
                            <option value="Sangat Rahasia" <?= old('klasifikasi_keamanan') == 'Sangat Rahasia' ? 'selected' : '' ?>>Sangat Rahasia</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="jra-form-label">Hak Akses</label>
                        <select name="hak_akses" class="form-select jra-form-control">
                            <option value="Terbuka" <?= old('hak_akses') == 'Terbuka' ? 'selected' : '' ?>>Terbuka</option>
                            <option value="Tertutup" <?= old('hak_akses') == 'Tertutup' ? 'selected' : '' ?>>Tertutup</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="jra-form-label">Akses Publik</label>
                        <input type="text" name="akses_publik" value="<?= old('akses_publik') ?>" class="jra-form-control" placeholder="Akses bagi publik jika ada">
                    </div>
                    <div class="col-md-4">
                        <label class="jra-form-label">Unit Pengolah</label>
                        <input type="text" name="unit_pengolah" value="<?= old('unit_pengolah') ?>" class="jra-form-control" placeholder="Contoh: Bagian Umum / Sekretariat">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="jra-form-label">Dasar Pertimbangan (Opsional)</label>
                    <textarea name="dasar_pertimbangan" class="jra-form-control" rows="2" placeholder="Dasar hukum / peraturan pertimbangan retensi..."><?= old('dasar_pertimbangan') ?></textarea>
                </div>

                <div class="d-flex align-items-center gap-2 pt-2 border-top">
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-medium" style="background-color: #1E60EC; border-color: #1E60EC; font-size: 13.5px;">
                        <i class="bi bi-save me-1"></i> Simpan JRA
                    </button>
                    <a href="<?= base_url('/jra') ?>" class="btn btn-light border px-4 py-2 rounded-3 text-secondary fw-medium" style="font-size: 13.5px;">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
