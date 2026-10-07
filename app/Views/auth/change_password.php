<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Ganti Password Default - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/change-password.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="change-password-wrapper">
    <!-- Header Halaman -->
    <div class="change-password-header">
        <h1 class="change-password-title">Ganti Password Default</h1>
        <p class="change-password-desc">Demi keamanan, Anda diwajibkan untuk mengganti password default yang diberikan oleh administrator sebelum dapat mengakses sistem.</p>
    </div>

    <!-- Flash Message Feedback -->
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert-feedback alert-danger" role="alert">
            <i class="bi bi-exclamation-circle-fill"></i>
            <div><?= session()->getFlashdata('error') ?></div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert-feedback alert-success" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <div><?= session()->getFlashdata('success') ?></div>
        </div>
    <?php endif; ?>

    <!-- Form Card Container -->
    <div class="change-password-card">
        <!-- Kotak Informasi Keamanan Akun -->
        <div class="security-info-box">
            <div class="security-info-icon-wrapper">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="security-info-content">
                <h4 class="security-info-title">Keamanan Akun</h4>
                <p class="security-info-text">Pastikan password baru Anda mudah diingat tetapi sulit ditebak.</p>
            </div>
        </div>

        <!-- Form Penggantian Password -->
        <form action="<?= base_url('/change-password/process') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Field Password Baru -->
            <div class="form-group-custom">
                <label for="new_password" class="form-label-custom">Password Baru</label>
                <div class="password-input-wrapper">
                    <i class="bi bi-lock input-icon-left"></i>
                    <input type="password" name="new_password" id="new_password" class="form-control-password has-left-icon" placeholder="Masukkan password baru" required minlength="8" autocomplete="new-password" oninput="checkPasswordStrength(this.value)">
                    <button type="button" class="btn-toggle-eye" onclick="toggleFieldVisibility('new_password', this)" aria-label="Tampilkan atau sembunyikan password baru">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Indikator Kekuatan Password -->
            <div class="password-strength-section">
                <div class="strength-header">
                    <span class="strength-label">Kekuatan password</span>
                    <span id="strengthText" class="strength-status">Kuat</span>
                </div>
                <div class="strength-bars">
                    <div class="strength-bar active-blue" id="bar1"></div>
                    <div class="strength-bar active-blue" id="bar2"></div>
                    <div class="strength-bar active-blue" id="bar3"></div>
                    <div class="strength-bar" id="bar4"></div>
                </div>
                <div class="strength-hint">
                    Minimal 8 karakter • Gunakan kombinasi huruf, angka, dan simbol.
                </div>
            </div>

            <!-- Field Konfirmasi Password Baru -->
            <div class="form-group-custom">
                <label for="confirm_password" class="form-label-custom">Konfirmasi Password Baru</label>
                <div class="password-input-wrapper">
                    <i class="bi bi-lock input-icon-left"></i>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control-password has-left-icon" placeholder="Masukkan kembali password baru" required minlength="8" autocomplete="new-password">
                    <button type="button" class="btn-toggle-eye" onclick="toggleFieldVisibility('confirm_password', this)" aria-label="Tampilkan atau sembunyikan konfirmasi password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Checkbox Tampilkan Password -->
            <div class="form-check-password">
                <input class="form-check-input" type="checkbox" id="show_password" onclick="toggleAllPasswords(this)">
                <label class="form-check-label" for="show_password">
                    Tampilkan password
                </label>
            </div>

            <!-- Tombol Aksi (Simpan Password & Logout) -->
            <div class="button-stack-custom">
                <button type="submit" class="btn-submit-action">
                    <i class="bi bi-check2"></i>
                    <span>Simpan Password</span>
                </button>
                <a href="<?= base_url('/logout') ?>" class="btn-logout-action">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </div>
        </form>

        <!-- Card Footer -->
        <div class="change-password-footer">
            <div class="footer-note">
                <i class="bi bi-info-circle"></i>
                <span>Password Anda akan digunakan untuk login pada akses berikutnya.</span>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function toggleFieldVisibility(fieldId, btn) {
        const input = document.getElementById(fieldId);
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

    function toggleAllPasswords(checkbox) {
        const pw1 = document.getElementById('new_password');
        const pw2 = document.getElementById('confirm_password');
        const eyeIcons = document.querySelectorAll('.btn-toggle-eye i');
        
        const targetType = checkbox.checked ? 'text' : 'password';
        if (pw1) pw1.type = targetType;
        if (pw2) pw2.type = targetType;

        eyeIcons.forEach(icon => {
            if (checkbox.checked) {
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    }

    function checkPasswordStrength(password) {
        const bars = [
            document.getElementById('bar1'),
            document.getElementById('bar2'),
            document.getElementById('bar3'),
            document.getElementById('bar4')
        ];
        const statusText = document.getElementById('strengthText');

        // Reset all bars
        bars.forEach(b => {
            b.className = 'strength-bar';
        });

        if (!password || password.length === 0) {
            statusText.textContent = '';
            return;
        }

        let score = 0;
        if (password.length >= 8) score++;
        if (/[A-Z]/.test(password) && /[a-z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;

        if (score === 1) {
            bars[0].classList.add('active-red');
            statusText.textContent = 'Lemah';
            statusText.style.color = '#EF4444';
        } else if (score === 2) {
            bars[0].classList.add('active-amber');
            bars[1].classList.add('active-amber');
            statusText.textContent = 'Cukup';
            statusText.style.color = '#F59E0B';
        } else if (score === 3) {
            bars[0].classList.add('active-blue');
            bars[1].classList.add('active-blue');
            bars[2].classList.add('active-blue');
            statusText.textContent = 'Kuat';
            statusText.style.color = '#1E60EC';
        } else if (score >= 4) {
            bars[0].classList.add('active-green');
            bars[1].classList.add('active-green');
            bars[2].classList.add('active-green');
            bars[3].classList.add('active-green');
            statusText.textContent = 'Sangat Kuat';
            statusText.style.color = '#10B981';
        }
    }
</script>
<?= $this->endSection() ?>
