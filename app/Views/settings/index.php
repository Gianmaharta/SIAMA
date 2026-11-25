<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Pengaturan Website - <?= esc($settings['site_name'] ?? 'SIAMA') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .ws-card {
        background-color: #FFFFFF;
        border: 1px solid #E5EAF1;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.03);
    }

    .ws-card-header {
        background-color: #FFFFFF;
        border-bottom: 1px solid #F1F5F9;
        padding: 18px 24px;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .ws-title-accent {
        width: 4px;
        height: 18px;
        background-color: #1E60EC;
        border-radius: 4px;
        flex-shrink: 0;
    }

    .ws-card-title {
        font-size: 15.5px;
        font-weight: 700;
        color: #0B1528;
        margin: 0;
    }

    .ws-card-body {
        padding: 22px 24px 24px;
    }

    .ws-label {
        font-size: 13px;
        font-weight: 600;
        color: #172033;
        margin-bottom: 6px;
    }

    .ws-control,
    .ws-select {
        height: 42px;
        background-color: #FFFFFF;
        border: 1px solid #E5EAF1;
        border-radius: 8px;
        font-size: 13.5px;
        color: #172033;
        padding: 8px 14px;
        transition: border-color 0.18s ease, box-shadow 0.18s ease;
    }

    .ws-select {
        padding-right: 38px;
        text-overflow: ellipsis;
    }

    .ws-textarea {
        background-color: #FFFFFF;
        border: 1px solid #E5EAF1;
        border-radius: 8px;
        font-size: 13.5px;
        color: #172033;
        padding: 10px 14px;
        line-height: 1.55;
        transition: border-color 0.18s ease, box-shadow 0.18s ease;
    }

    .ws-control:focus,
    .ws-select:focus,
    .ws-textarea:focus {
        border-color: #1E60EC;
        box-shadow: 0 0 0 3px rgba(30, 96, 236, 0.1);
    }

    .ws-input-group .ws-control {
        border-right: 0;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .ws-input-group .ws-pw-toggle {
        height: 42px;
        background-color: #FFFFFF;
        border: 1px solid #E5EAF1;
        border-left: 0;
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
        color: #64748B;
        padding: 0 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: color 0.18s ease, background-color 0.18s ease;
    }

    .ws-input-group .ws-pw-toggle:hover {
        color: #0B1528;
        background-color: #F8FAFC;
    }

    .ws-input-group .ws-control:focus + .ws-pw-toggle {
        border-color: #1E60EC;
    }

    .ws-logo-box {
        background-color: #FFFFFF;
        border: 1px solid #E5EAF1;
        border-radius: 10px;
        padding: 14px 16px;
    }

    .ws-logo-preview-wrap {
        width: 112px;
        height: 54px;
        border-radius: 8px;
        background-color: #0B1528;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        flex-shrink: 0;
    }

    .ws-logo-preview-img {
        max-height: 40px;
        max-width: 96px;
        object-fit: contain;
    }

    .ws-help-text {
        font-size: 12px;
        color: #64748B;
        line-height: 1.5;
        margin-top: 6px;
    }

    .ws-btn-save {
        height: 44px;
        background-color: #1E60EC;
        border-color: #1E60EC;
        color: #FFFFFF;
        font-size: 13.5px;
        font-weight: 600;
        border-radius: 8px;
        transition: background-color 0.18s ease, border-color 0.18s ease;
    }

    .ws-btn-save:hover,
    .ws-btn-save:focus {
        background-color: #154EC2;
        border-color: #154EC2;
        color: #FFFFFF;
    }

    .ws-btn-public {
        height: 38px;
        background-color: #1E60EC;
        border: 1px solid #1E60EC;
        color: #FFFFFF;
        font-size: 13px;
        font-weight: 600;
        border-radius: 8px;
        padding: 0 16px;
        transition: background-color 0.18s ease, border-color 0.18s ease;
    }

    .ws-btn-public:hover,
    .ws-btn-public:focus {
        background-color: #154EC2;
        border-color: #154EC2;
        color: #FFFFFF;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1" style="color: #0B1528; letter-spacing: -0.3px;">Pengaturan Website &amp; Sistem</h1>
            <p class="text-secondary small mb-0">Kelola identitas branding, logo aplikasi, informasi kontak footer, dan kebijakan password default secara dinamis.</p>
        </div>
        <a href="<?= base_url('/') ?>" target="_blank" class="btn ws-btn-public d-inline-flex align-items-center gap-2">
            <i class="bi bi-box-arrow-up-right"></i>
            <span>Lihat Landing Page Publik</span>
        </a>
    </div>

    <form action="<?= base_url('/settings/update') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="row g-4 align-items-start">
            <!-- KOLOM KIRI: IDENTITAS & KONTAK -->
            <div class="col-12 col-lg-7">
                <!-- CARD 1: IDENTITAS & BRANDING APLIKASI -->
                <div class="card ws-card mb-4">
                    <div class="card-header ws-card-header d-flex align-items-center gap-2">
                        <span class="ws-title-accent" aria-hidden="true"></span>
                        <h2 class="ws-card-title">Identitas &amp; Branding Aplikasi</h2>
                    </div>
                    <div class="card-body ws-card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-5">
                                <label for="site_name" class="form-label ws-label">
                                    Nama Aplikasi <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="site_name" id="site_name" class="form-control ws-control" value="<?= esc($settings['site_name'] ?? 'SIAMA') ?>" required>
                            </div>
                            <div class="col-12 col-md-7">
                                <label for="site_tagline" class="form-label ws-label">
                                    Tagline / Kepanjangan Aplikasi <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="site_tagline" id="site_tagline" class="form-control ws-control" value="<?= esc($settings['site_tagline'] ?? 'Sistem Informasi Alih Media Arsip') ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="instansi_name" class="form-label ws-label">Nama Instansi / Pemerintah Daerah</label>
                                <input type="text" name="instansi_name" id="instansi_name" class="form-control ws-control" value="<?= esc($settings['instansi_name'] ?? 'Pemerintah Kabupaten Buleleng') ?>">
                            </div>
                            <div class="col-12">
                                <label for="site_description" class="form-label ws-label">Deskripsi Singkat Aplikasi (Tampil di Landing Page)</label>
                                <textarea name="site_description" id="site_description" rows="3" class="form-control ws-textarea"><?= esc($settings['site_description'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <label for="site_logo_file" class="form-label ws-label">Logo Website / Branding</label>
                                <div class="ws-logo-box d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                                    <?php
                                    $logoPath = $settings['site_logo'] ?? 'logo siama1.png';
                                    $logoUrl = file_exists(FCPATH . $logoPath)
                                        ? base_url($logoPath)
                                        : (file_exists(FCPATH . 'Images/logo siama1.png') ? base_url('Images/logo siama1.png') : base_url('logo siama1.png'));
                                    ?>
                                    <div class="ws-logo-preview-wrap">
                                        <img src="<?= esc($logoUrl) ?>" alt="Logo Saat Ini" id="logoPreviewImg" class="ws-logo-preview-img">
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <input type="file" name="site_logo_file" id="site_logo_file" class="form-control form-control-sm" accept=".png,.jpg,.jpeg,.svg,.webp">
                                        <div class="ws-help-text">Biarkan kosong jika tidak ingin mengganti logo. Format: PNG, JPG, SVG, WEBP.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: INFORMASI KONTAK & SOSIAL MEDIA -->
                <div class="card ws-card">
                    <div class="card-header ws-card-header d-flex align-items-center gap-2">
                        <span class="ws-title-accent" aria-hidden="true"></span>
                        <h2 class="ws-card-title">Informasi Kontak &amp; Sosial Media (Footer Landing Page)</h2>
                    </div>
                    <div class="card-body ws-card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="contact_address" class="form-label ws-label">Alamat Kantor / Instansi</label>
                                <input type="text" name="contact_address" id="contact_address" class="form-control ws-control" value="<?= esc($settings['contact_address'] ?? '') ?>" placeholder="Jl. Pahlawan No. 1, Singaraja...">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="contact_phone" class="form-label ws-label">Nomor Telepon / HP</label>
                                <input type="text" name="contact_phone" id="contact_phone" class="form-control ws-control" value="<?= esc($settings['contact_phone'] ?? '') ?>" placeholder="(0362) 21985">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="contact_email" class="form-label ws-label">Email Resmi</label>
                                <input type="email" name="contact_email" id="contact_email" class="form-control ws-control" value="<?= esc($settings['contact_email'] ?? '') ?>" placeholder="kominfosanti@bulelengkab.go.id">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="social_instagram" class="form-label ws-label">Tautan Instagram</label>
                                <input type="url" name="social_instagram" id="social_instagram" class="form-control ws-control" value="<?= esc($settings['social_instagram'] ?? '') ?>" placeholder="https://instagram.com/...">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="social_facebook" class="form-label ws-label">Tautan Facebook</label>
                                <input type="url" name="social_facebook" id="social_facebook" class="form-control ws-control" value="<?= esc($settings['social_facebook'] ?? '') ?>" placeholder="https://facebook.com/...">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="social_youtube" class="form-label ws-label">Tautan YouTube</label>
                                <input type="url" name="social_youtube" id="social_youtube" class="form-control ws-control" value="<?= esc($settings['social_youtube'] ?? '') ?>" placeholder="https://youtube.com/...">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="social_website" class="form-label ws-label">Website Resmi Pemkab</label>
                                <input type="url" name="social_website" id="social_website" class="form-control ws-control" value="<?= esc($settings['social_website'] ?? '') ?>" placeholder="https://bulelengkab.go.id">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: KEBIJAKAN PASSWORD DEFAULT & RESET -->
            <div class="col-12 col-lg-5">
                <div class="card ws-card">
                    <div class="card-header ws-card-header d-flex align-items-center gap-2">
                        <span class="ws-title-accent" aria-hidden="true"></span>
                        <h2 class="ws-card-title">Kebijakan Password Default &amp; Reset</h2>
                    </div>
                    <div class="card-body ws-card-body">
                        <div class="mb-3">
                            <label for="password_mode" class="form-label ws-label">Mekanisme Password Saat Buat / Reset Akun</label>
                            <?php $pwMode = $settings['password_mode'] ?? 'default'; ?>
                            <select name="password_mode" id="password_mode" class="form-select ws-select">
                                <option value="default" <?= $pwMode === 'default' ? 'selected' : '' ?>>Gunakan Password Default Pengaturan Website</option>
                                <option value="random" <?= $pwMode === 'random' ? 'selected' : '' ?>>Generate Password Random Otomatis (Tampil &amp; Copy)</option>
                            </select>
                            <div class="ws-help-text">
                                Admin tetap dapat memilih antara Password Default atau Password Random saat membuat akun baru.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="default_password" class="form-label ws-label">
                                Password Default Sistem <span class="text-danger">*</span>
                            </label>
                            <div class="input-group ws-input-group">
                                <input type="text" name="default_password" id="default_password" class="form-control ws-control font-monospace" value="<?= esc($settings['default_password'] ?? 'Admin123!') ?>" minlength="6" required>
                                <button type="button" class="btn ws-pw-toggle" id="toggleDefaultPwBtn" aria-label="Tampilkan atau sembunyikan password" title="Tampilkan / Sembunyikan Password">
                                    <i class="bi bi-eye-slash" id="toggleDefaultPwIcon"></i>
                                </button>
                            </div>
                            <div class="ws-help-text">
                                Digunakan secara dinamis saat pembuatan akun (mode Default) maupun ketika tombol <strong>Reset Password</strong> diklik. Pengguna dengan password ini wajib mengganti password saat pertama kali login.
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn ws-btn-save d-inline-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-floppy-fill"></i>
                                <span>Simpan Seluruh Pengaturan Website</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle tampil / sembunyikan password default
    const pwInput = document.getElementById('default_password');
    const pwBtn   = document.getElementById('toggleDefaultPwBtn');
    const pwIcon  = document.getElementById('toggleDefaultPwIcon');

    if (pwInput && pwBtn && pwIcon) {
        pwBtn.addEventListener('click', function() {
            const isText = pwInput.type === 'text';
            pwInput.type = isText ? 'password' : 'text';
            pwIcon.classList.toggle('bi-eye-slash', !isText);
            pwIcon.classList.toggle('bi-eye', isText);
        });
    }

    // Live preview logo saat memilih file baru
    const logoInput   = document.getElementById('site_logo_file');
    const logoPreview = document.getElementById('logoPreviewImg');

    if (logoInput && logoPreview) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files && e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(evt) {
                if (evt.target && evt.target.result) {
                    logoPreview.src = evt.target.result;
                }
            };
            reader.readAsDataURL(file);
        });
    }
});
</script>
<?= $this->endSection() ?>
