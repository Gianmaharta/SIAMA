<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Alih Media (SIAMA)</title>
    
    <!-- Google Fonts Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('css/login.css') ?>">
</head>
<body>
    <div class="login-wrapper d-flex flex-column justify-content-between align-items-center min-vh-100 position-relative">
        <div class="overlay"></div>

        <main class="login-main container d-flex align-items-center justify-content-center flex-grow-1 position-relative z-2 py-4">
            <div class="login-card p-4 p-sm-5">
                <div class="login-header text-center mb-4">
                    <h1 class="login-title fw-bold">LOGIN</h1>
                    <p class="login-subtitle mb-0">Sistem Informasi Alih Media (SIAMA)</p>
                </div>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 mb-4 rounded-3 border-0" role="alert">
                        <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                        <div class="small fw-medium">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success d-flex align-items-center gap-2 py-2 px-3 mb-4 rounded-3 border-0" role="alert">
                        <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                        <div class="small fw-medium">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('/login/process') ?>" method="POST" class="login-form">
                    <?= csrf_field() ?>

                    <div class="mb-3 text-start">
                        <label for="email" class="form-label small fw-medium">Email</label>
                        <div class="input-group">
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-control" 
                                placeholder="Masukkan email Anda" 
                                value="<?= old('email') ?>" 
                                required 
                                autocomplete="email"
                            >
                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-4 text-start">
                        <label for="password" class="form-label small fw-medium">Password</label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-control" 
                                placeholder="Masukkan password Anda" 
                                required 
                                autocomplete="current-password"
                            >
                            <button 
                                type="button" 
                                class="btn input-group-text" 
                                onclick="togglePassword()" 
                                id="togglePasswordBtn" 
                                aria-label="Tampilkan atau sembunyikan password"
                            >
                                <i id="eyeIcon" class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login w-100 rounded-pill py-2 fw-semibold">
                        <span>Login &rarr;</span>
                    </button>
                </form>
            </div>
        </main>

        <footer class="login-footer text-center position-relative z-2 py-2">
            <p class="mb-0 text-white-50 small">Hak Cipta &copy; 2026 Pemerintah Kabupaten Buleleng.</p>
        </footer>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            var pwd = document.getElementById("password");
            var eyeIcon = document.getElementById("eyeIcon");
            
            if (pwd.type === "password") {
                pwd.type = "text";
                eyeIcon.classList.remove("bi-eye");
                eyeIcon.classList.add("bi-eye-slash");
            } else {
                pwd.type = "password";
                eyeIcon.classList.remove("bi-eye-slash");
                eyeIcon.classList.add("bi-eye");
            }
        }
    </script>
</body>
</html>
