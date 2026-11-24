<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Pengaturan Website - <?= esc($settings['site_name'] ?? 'SIAMA') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
        <div class="fw-medium small"><?= session()->getFlashdata('success') ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
        <div class="fw-medium small"><?= session()->getFlashdata('error') ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1" style="font-size: 24px; letter-spacing: -0.3px;">Pengaturan Website & Sistem</h1>
        <p class="text-secondary small mb-0">Kelola identitas branding, logo aplikasi, informasi kontak footer, dan kebijakan password default secara dinamis.</p>
    </div>
    <a href="<?= base_url('/') ?>" target="_blank" class="btn btn-outline-primary btn-sm px-3 py-2 rounded-3 fw-medium d-inline-flex align-items-center gap-2">
        <i class="bi bi-box-arrow-up-right"></i>
        <span>Lihat Landing Page Publik</span>
    </a>
</div>

<form action="<?= base_url('/settings/update') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- KOLOM KIRI: BRANDING & KONTAK FOOTER -->
        <div class="col-12 col-xl-7">
            <!-- CARD 1: IDENTITAS & BRANDING WEBSITE -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom px-4 py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-palette2 text-primary fs-5"></i>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">Identitas & Branding Aplikasi</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-5">
                            <label for="site_name" class="form-label fw-semibold small text-dark">Nama Aplikasi <span class="text-danger">*</span></label>
                            <input type="text" name="site_name" id="site_name" class="form-control" value="<?= esc($settings['site_name'] ?? 'SIAMA') ?>" required>
                        </div>
                        <div class="col-12 col-md-7">
                            <label for="site_tagline" class="form-label fw-semibold small text-dark">Tagline / Kepanjangan Aplikasi <span class="text-danger">*</span></label>
                            <input type="text" name="site_tagline" id="site_tagline" class="form-control" value="<?= esc($settings['site_tagline'] ?? 'Sistem Informasi Alih Media Arsip') ?>" required>
                        </div>
                        <div class="col-12">
                            <label for="instansi_name" class="form-label fw-semibold small text-dark">Nama Instansi / Pemerintah Daerah</label>
                            <input type="text" name="instansi_name" id="instansi_name" class="form-control" value="<?= esc($settings['instansi_name'] ?? 'Pemerintah Kabupaten Buleleng') ?>">
                        </div>
                        <div class="col-12">
                            <label for="site_description" class="form-label fw-semibold small text-dark">Deskripsi Singkat Aplikasi (Tampil di Landing Page)</label>
                            <textarea name="site_description" id="site_description" rows="3" class="form-control"><?= esc($settings['site_description'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label for="site_logo_file" class="form-label fw-semibold small text-dark">Logo Website / Branding</label>
                            <div class="d-flex align-items-center gap-3 flex-wrap p-3 rounded-3 bg-light border">
                                <?php
                                $logoPath = $settings['site_logo'] ?? 'logo siama1.png';
                                $logoUrl = file_exists(FCPATH . $logoPath) ? base_url($logoPath) : (file_exists(FCPATH . 'Images/logo siama1.png') ? base_url('Images/logo siama1.png') : base_url('logo siama1.png'));
                                ?>
                                <div class="p-2 rounded-3 bg-dark d-inline-flex align-items-center justify-content-center" style="min-width: 110px; height: 52px;">
                                    <img src="<?= esc($logoUrl) ?>" alt="Logo Saat Ini" style="max-height: 38px; max-width: 140px; object-fit: contain;">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="site_logo_file" id="site_logo_file" class="form-control form-control-sm" accept=".png,.jpg,.jpeg,.svg,.webp">
                                    <div class="form-text small text-muted mt-1">Biarkan kosong jika tidak ingin mengganti logo. Format: PNG, JPG, SVG, WEBP.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 2: KONTAK & SOSIAL MEDIA FOOTER -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom px-4 py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt-fill text-primary fs-5"></i>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">Informasi Kontak & Sosial Media (Footer Landing Page)</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="contact_address" class="form-label fw-semibold small text-dark">Alamat Kantor / Instansi</label>
                            <input type="text" name="contact_address" id="contact_address" class="form-control" value="<?= esc($settings['contact_address'] ?? '') ?>" placeholder="Jl. Pahlawan No. 1, Singaraja...">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="contact_phone" class="form-label fw-semibold small text-dark">Nomor Telepon / HP</label>
                            <input type="text" name="contact_phone" id="contact_phone" class="form-control" value="<?= esc($settings['contact_phone'] ?? '') ?>" placeholder="(0362) 21985">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="contact_email" class="form-label fw-semibold small text-dark">Email Resmi</label>
                            <input type="email" name="contact_email" id="contact_email" class="form-control" value="<?= esc($settings['contact_email'] ?? '') ?>" placeholder="kominfosanti@bulelengkab.go.id">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="social_instagram" class="form-label fw-semibold small text-dark"><i class="bi bi-instagram text-danger me-1"></i>Tautan Instagram</label>
                            <input type="url" name="social_instagram" id="social_instagram" class="form-control" value="<?= esc($settings['social_instagram'] ?? '') ?>" placeholder="https://instagram.com/...">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="social_facebook" class="form-label fw-semibold small text-dark"><i class="bi bi-facebook text-primary me-1"></i>Tautan Facebook</label>
                            <input type="url" name="social_facebook" id="social_facebook" class="form-control" value="<?= esc($settings['social_facebook'] ?? '') ?>" placeholder="https://facebook.com/...">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="social_youtube" class="form-label fw-semibold small text-dark"><i class="bi bi-youtube text-danger me-1"></i>Tautan YouTube</label>
                            <input type="url" name="social_youtube" id="social_youtube" class="form-control" value="<?= esc($settings['social_youtube'] ?? '') ?>" placeholder="https://youtube.com/...">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="social_website" class="form-label fw-semibold small text-dark"><i class="bi bi-globe2 text-info me-1"></i>Website Resmi Pemkab</label>
                            <input type="url" name="social_website" id="social_website" class="form-control" value="<?= esc($settings['social_website'] ?? '') ?>" placeholder="https://bulelengkab.go.id">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: OTENTIKASI SSO & KEBIJAKAN PASSWORD DEFAULT -->
        <div class="col-12 col-xl-5">
            <!-- CARD 3: KEBIJAKAN PASSWORD DEFAULT & RESET PASSWORD -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom px-4 py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-shield-lock-fill text-primary fs-5"></i>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">Kebijakan Password Default & Reset</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="password_mode" class="form-label fw-semibold small text-dark">Mekanisme Password Saat Buat / Reset Akun</label>
                        <?php $pwMode = $settings['password_mode'] ?? 'default'; ?>
                        <select name="password_mode" id="password_mode" class="form-select">
                            <option value="default" <?= $pwMode === 'default' ? 'selected' : '' ?>>Gunakan Password Default Pengaturan Website</option>
                            <option value="random" <?= $pwMode === 'random' ? 'selected' : '' ?>>Generate Password Random Otomatis (Tampil & Copy)</option>
                        </select>
                        <div class="form-text small text-muted mt-1">
                            Admin tetap dapat memilih antara Password Default atau Password Random saat membuat akun baru.
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="default_password" class="form-label fw-semibold small text-dark">Password Default Sistem <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="default_password" id="default_password" class="form-control font-monospace" value="<?= esc($settings['default_password'] ?? 'Admin123!') ?>" minlength="6" required>
                            <span class="input-group-text bg-light text-secondary small"><i class="bi bi-key-fill"></i></span>
                        </div>
                        <div class="form-text small text-muted mt-1">
                            Digunakan secara dinamis saat pembuatan akun (mode Default) maupun ketika tombol <strong>Reset Password</strong> diklik. Pengguna dengan password ini wajib mengganti password saat pertama kali login.
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOMBOL SIMPAN -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary py-2.5 px-4 rounded-3 fw-semibold shadow-sm d-inline-flex align-items-center justify-content-center gap-2" style="background-color: #1E60EC; border-color: #1E60EC; height: 46px;">
                    <i class="bi bi-floppy-fill"></i>
                    <span>Simpan Seluruh Pengaturan Website</span>
                </button>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>
