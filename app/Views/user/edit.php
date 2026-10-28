<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Pengguna - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/user-master.css?v=' . (file_exists(FCPATH . 'css/user-master.css') ? filemtime(FCPATH . 'css/user-master.css') : time())) ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header Halaman: Tombol Back Sejajar Judul -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('/users') ?>" class="btn-back-title" title="Kembali ke Manajemen Akun" aria-label="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="user-page-header mb-0">
        <h1 class="user-page-title mb-0">Edit Pengguna</h1>
        <p class="user-page-subtitle mb-0">Perbarui data akun pengguna <?= esc($user['nama']) ?>.</p>
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
    <div class="col-12 col-lg-8 col-xl-7">
        <div class="user-table-card p-4">
            <form action="<?= base_url('/users/update/' . $user['id_user']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="nama" class="user-form-label">
                        Nama Lengkap <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        id="nama" 
                        class="user-form-control" 
                        value="<?= old('nama', $user['nama']) ?>" 
                        required 
                        autocomplete="off"
                    >
                </div>

                <div class="mb-3">
                    <label for="email" class="user-form-label">
                        Email <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        class="user-form-control" 
                        value="<?= old('email', $user['email']) ?>" 
                        required 
                        autocomplete="off"
                    >
                </div>

                <div class="mb-3">
                    <label for="id_role" class="user-form-label">
                        Hak Akses (Role) <span class="text-danger">*</span>
                    </label>
                    <select name="id_role" id="id_role" class="user-form-control user-form-select" required>
                        <option value="">-- Pilih Role --</option>
                        <?php foreach ($roles as $r) : ?>
                            <option value="<?= esc($r['id_role']) ?>" <?= (old('id_role', $current_role) == $r['id_role']) ? 'selected' : '' ?>>
                                <?= esc($r['nama_role']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="id_opd" class="user-form-label">
                        Perangkat Daerah (OPD)
                    </label>
                    <?php if ($userRole === 'Admin_OPD') : ?>
                        <input type="hidden" name="id_opd" value="<?= esc(session()->get('id_opd')) ?>">
                        <input type="text" class="user-form-control bg-light text-muted" value="[Terkunci pada OPD Anda]" readonly disabled style="cursor: not-allowed;">
                    <?php else : ?>
                        <select name="id_opd" id="id_opd" class="user-form-control user-form-select">
                            <option value="">-- Tidak Terikat OPD (Admin Pemkab) --</option>
                            <?php foreach ($opds as $o) : ?>
                                <option value="<?= esc($o['id_opd']) ?>" <?= (old('id_opd', $user['id_opd']) == $o['id_opd']) ? 'selected' : '' ?>>
                                    <?= esc($o['nama_opd']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label for="is_active" class="user-form-label">Status Keaktifan</label>
                    <select name="is_active" id="is_active" class="user-form-control user-form-select">
                        <option value="1" <?= (old('is_active', $user['is_active']) == 1) ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= (old('is_active', $user['is_active']) == 0) ? 'selected' : '' ?>>Tidak Aktif</option>
                    </select>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                    <a href="<?= base_url('/users/reset-password/' . $user['id_user']) ?>" class="btn btn-outline-warning btn-sm" onclick="return confirm('Reset password pengguna ini ke default (Admin123!)?')">
                        <i class="bi bi-key me-1"></i> Reset Password ke Default
                    </a>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('/users') ?>" class="user-btn-cancel">Batal</a>
                        <button type="submit" class="user-btn-add px-4">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
