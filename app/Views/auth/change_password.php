<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Ganti Password Default - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/change-password.css?v=' . time()) ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0 py-2">
    <div class="row justify-content-center">
        <div class="col-12 col-md-11 col-lg-8 col-xl-7" style="max-width: 660px;">

            <!-- Flash Message Feedback -->
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3 rounded-3 border-0 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
                    <div class="small fw-medium"><?= session()->getFlashdata('error') ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3 rounded-3 border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                    <div class="small fw-medium"><?= session()->getFlashdata('success') ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>

            <!-- Kotak Informasi Peringatan Keamanan Wajib -->
            <div class="alert border-0 rounded-3 p-3 mb-4 d-flex align-items-start gap-3 shadow-sm" style="background-color: #FFFBEB; border: 1px solid #FEF3C7 !important;">
                <div class="d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="color: #D97706; font-size: 20px;">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1" style="font-size: 14px; color: #1E293B;">Peringatan Keamanan Wajib</h6>
                    <p class="mb-0 text-secondary" style="font-size: 13px; line-height: 1.5; color: #475569 !important;">
                        Demi keamanan sistem kearsipan digital Pemkab Buleleng, Anda diwajibkan mengganti kata sandi awal (default) sebelum mengakses fitur SIAMA lainnya.
                    </p>
                </div>
            </div>

            <!-- Card Form Ubah Password -->
            <div class="card border-0 rounded-4 shadow-sm" style="background-color: #FFFFFF; border: 1px solid #E2E8F0 !important; box-shadow: 0 4px 20px rgba(11, 21, 40, 0.04) !important;">
                <div class="card-body p-4 p-sm-5">
                    
                    <!-- Card Header: Judul & Badge Wajib Diperbarui -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                        <h2 class="h4 fw-bold text-dark mb-0" style="letter-spacing: -0.2px; font-size: 21px;">Ganti Password Default</h2>
                        <span class="badge rounded-pill fw-semibold px-3 py-1.5" style="background-color: #FEE2E2; color: #DC2626; font-size: 12px;">
                            Wajib Diperbarui
                        </span>
                    </div>
                    <p class="text-secondary small mb-4" style="font-size: 13.5px; color: #64748B;">
                        Silakan perbarui kata sandi default Anda untuk melanjutkan akses sistem SIAMA.
                    </p>

                    <!-- Form Penggantian Password -->
                    <form action="<?= base_url('/change-password/process') ?>" method="post" id="formChangePassword">
                        <?= csrf_field() ?>

                        <!-- 1. Password Lama (Default) -->
                        <div class="mb-3">
                            <label for="old_password" class="form-label small fw-semibold text-dark mb-1.5">
                                Password Lama (Default) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #CBD5E1; border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input 
                                    type="password" 
                                    name="old_password" 
                                    id="old_password" 
                                    class="form-control border-start-0 border-end-0 text-dark" 
                                    placeholder="Masukkan password bawaan/sementara" 
                                    style="border-color: #CBD5E1; font-size: 13.5px; height: 46px;"
                                    autocomplete="current-password"
                                >
                                <button 
                                    type="button" 
                                    class="btn btn-outline-secondary bg-white border-start-0 text-muted btn-eye-toggle" 
                                    onclick="toggleVisibility('old_password', this)"
                                    style="border-color: #CBD5E1; border-top-right-radius: 10px; border-bottom-right-radius: 10px;"
                                    aria-label="Tampilkan atau sembunyikan password lama"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- 2. Password Baru -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label small fw-semibold text-dark mb-1.5">
                                Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #CBD5E1; border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input 
                                    type="password" 
                                    name="new_password" 
                                    id="new_password" 
                                    class="form-control border-start-0 border-end-0 text-dark" 
                                    placeholder="Masukkan password baru minimal 8 karakter" 
                                    style="border-color: #CBD5E1; font-size: 13.5px; height: 46px;"
                                    required 
                                    minlength="8" 
                                    autocomplete="new-password"
                                >
                                <button 
                                    type="button" 
                                    class="btn btn-outline-secondary bg-white border-start-0 text-muted btn-eye-toggle" 
                                    onclick="toggleVisibility('new_password', this)"
                                    style="border-color: #CBD5E1; border-top-right-radius: 10px; border-bottom-right-radius: 10px;"
                                    aria-label="Tampilkan atau sembunyikan password baru"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- 3. Konfirmasi Password Baru -->
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label small fw-semibold text-dark mb-1.5">
                                Konfirmasi Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #CBD5E1; border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input 
                                    type="password" 
                                    name="confirm_password" 
                                    id="confirm_password" 
                                    class="form-control border-start-0 border-end-0 text-dark" 
                                    placeholder="Ulangi password baru Anda" 
                                    style="border-color: #CBD5E1; font-size: 13.5px; height: 46px;"
                                    required 
                                    minlength="8" 
                                    autocomplete="new-password"
                                >
                                <button 
                                    type="button" 
                                    class="btn btn-outline-secondary bg-white border-start-0 text-muted btn-eye-toggle" 
                                    onclick="toggleVisibility('confirm_password', this)"
                                    style="border-color: #CBD5E1; border-top-right-radius: 10px; border-bottom-right-radius: 10px;"
                                    aria-label="Tampilkan atau sembunyikan konfirmasi password"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Checkbox: Tampilkan Semua Password -->
                        <div class="form-check mb-4 mt-2">
                            <input class="form-check-input" type="checkbox" id="showAllPasswords" onchange="toggleAllPasswords(this)" style="cursor: pointer;">
                            <label class="form-check-label small text-secondary fw-medium" for="showAllPasswords" style="font-size: 13px; cursor: pointer;">
                                Tampilkan Semua Password
                            </label>
                        </div>

                        <!-- Tombol Aksi: Simpan Password (Kiri) & Logout (Kanan) -->
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pt-1">
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2 fw-semibold text-white shadow-sm" style="background-color: #1E60EC; border-color: #1E60EC; border-radius: 10px; font-size: 13.5px; height: 44px;">
                                <i class="bi bi-check2 fs-5"></i>
                                <span>Simpan Password</span>
                            </button>

                            <button type="button" class="btn btn-danger d-inline-flex align-items-center gap-2 px-4 py-2 fw-semibold text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#modalLogout" style="background-color: #DC2626; border-color: #DC2626; border-radius: 10px; font-size: 13.5px; height: 44px;">
                                <i class="bi bi-box-arrow-right fs-6"></i>
                                <span>Logout</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer Bantuan -->
            <div class="text-center mt-4">
                <p class="text-secondary small mb-0" style="font-size: 12.5px;">
                    Butuh bantuan? Hubungi <span class="fw-semibold text-primary">Tim Helpdesk Diskominfosanti Kab. Buleleng</span>
                </p>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Toggle visibilitas satu input password
    function toggleVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // Toggle semua input password sekaligus lewat checkbox
    function toggleAllPasswords(checkbox) {
        const fields = ['old_password', 'new_password', 'confirm_password'];
        const isShow = checkbox.checked;

        fields.forEach(function(id) {
            const el = document.getElementById(id);
            if (el) {
                el.type = isShow ? 'text' : 'password';
            }
        });

        // Sesuaikan seluruh ikon mata
        const eyeButtons = document.querySelectorAll('.btn-eye-toggle i');
        eyeButtons.forEach(function(icon) {
            if (isShow) {
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    }
</script>
<?= $this->endSection() ?>
