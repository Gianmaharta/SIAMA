<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?? 'SIAMA - Sistem Informasi Alih Media Arsip' ?></title>
    
    <!-- Google Fonts Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Dashboard CSS -->
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <div class="app-layout">
        <!-- Sidebar Kiri -->
        <?= $this->include('layouts/sidebar') ?>

        <!-- Area Konten Kanan -->
        <div class="main-wrapper">
            <!-- Top Navbar -->
            <?= $this->include('layouts/header') ?>

            <!-- Konten Halaman -->
            <main class="main-content">
                <?= $this->renderSection('content') ?>
            </main>

            <!-- Footer -->
            <?= $this->include('layouts/footer') ?>
        </div>
    </div>

    <!-- Modal Konfirmasi Logout -->
    <div class="modal fade" id="modalLogout" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 360px;">
            <div class="modal-content text-center p-3 border-0" style="border-radius: 16px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);">
                <div class="modal-body p-2 pt-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 54px; height: 54px; background-color: #FEE2E2; color: #DC2626; font-size: 24px;">
                        <i class="bi bi-box-arrow-right"></i>
                    </div>
                    <h5 class="fw-bold mb-2 text-dark" id="modalLogoutLabel" style="font-size: 18px;">Yakin ingin keluar?</h5>
                    <p class="text-secondary small mb-4">Apakah Anda yakin ingin keluar dari sistem SIAMA?</p>
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <button type="button" class="btn btn-light border px-3 py-2 rounded-3 text-secondary fw-medium flex-fill" data-bs-dismiss="modal" style="font-size: 13.5px;">
                            Batal
                        </button>
                        <a href="<?= base_url('/logout') ?>" class="btn btn-danger px-3 py-2 rounded-3 fw-medium flex-fill d-inline-flex align-items-center justify-content-center gap-1" style="font-size: 13.5px; background-color: #DC2626; border-color: #DC2626;">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const sidebar = document.getElementById('appSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('sidebar-open');
                if (backdrop) backdrop.classList.toggle('show');
            });
        }

        if (backdrop && sidebar) {
            backdrop.addEventListener('click', function() {
                sidebar.classList.remove('sidebar-open');
                backdrop.classList.remove('show');
            });
        }

        // Listener logout modal agar selalu bisa dipencet lancar dari dropdown manapun
        document.addEventListener('click', function(e) {
            const logoutTrigger = e.target.closest('[data-bs-target="#modalLogout"]');
            if (logoutTrigger) {
                e.preventDefault();
                const modalEl = document.getElementById('modalLogout');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            }

            // Global Handler: Buka kalender saat klik di border, teks, maupun area manapun dari input tanggal
            const dateInput = e.target.closest('input[type="date"]');
            if (dateInput && typeof dateInput.showPicker === 'function') {
                try {
                    dateInput.showPicker();
                } catch (err) {
                    // Abaikan jika picker sudah terbuka atau tidak didukung
                }
            }
        });
    });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
