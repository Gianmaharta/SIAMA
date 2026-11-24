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
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css?v=' . (file_exists(FCPATH . 'css/dashboard.css') ? filemtime(FCPATH . 'css/dashboard.css') : time())) ?>">
    <link rel="stylesheet" href="<?= base_url('css/notification-master.css?v=' . (file_exists(FCPATH . 'css/notification-master.css') ? filemtime(FCPATH . 'css/notification-master.css') : time())) ?>">
    <link rel="stylesheet" href="<?= base_url('css/siama-swal.css?v=' . (file_exists(FCPATH . 'css/siama-swal.css') ? filemtime(FCPATH . 'css/siama-swal.css') : time())) ?>">
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <!-- Carrier Flashdata Terpusat untuk SweetAlert2 -->
    <div id="siama-flash-data" class="d-none"
        data-success="<?= esc(session()->getFlashdata('success') ?? '') ?>"
        data-error="<?= esc(session()->getFlashdata('error') ?? '') ?>"
        data-warning="<?= esc(session()->getFlashdata('warning') ?? '') ?>"
        data-info="<?= esc(session()->getFlashdata('info') ?? '') ?>"
        data-errors="<?= esc(is_array(session()->getFlashdata('errors')) ? implode('<br>', session()->getFlashdata('errors')) : (session()->getFlashdata('errors') ?? '')) ?>"
    ></div>

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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (session()->getFlashdata('generated_password')) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Password Random Berhasil Dibuat',
                html: `
                    <div class="text-start small p-3 rounded-3 bg-light border mb-2">
                        <div class="text-muted mb-1">Pengguna:</div>
                        <div class="fw-bold text-dark mb-2"><?= esc(session()->getFlashdata('generated_user_name') ?? '-') ?> (<?= esc(session()->getFlashdata('generated_user_email') ?? '-') ?>)</div>
                        <div class="text-muted mb-1">Password Sementara (Wajib Ganti Saat Login):</div>
                        <div class="d-flex align-items-center justify-content-between gap-2 p-2 bg-white rounded border">
                            <code class="fs-5 fw-bold text-primary" id="swalGeneratedPw"><?= esc(session()->getFlashdata('generated_password')) ?></code>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="navigator.clipboard.writeText(document.getElementById('swalGeneratedPw').textContent); this.innerHTML='<i class=\'bi bi-check2\'></i> Tersalin';">
                                <i class="bi bi-clipboard"></i> Salin
                            </button>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Selesai',
                confirmButtonColor: '#1E60EC'
            });
        <?php elseif (session()->getFlashdata('success')) : ?>
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: <?= json_encode(strip_tags((string) session()->getFlashdata('success'))) ?>,
                    timer: 2600,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                    timerProgressBar: true
                });
            }
        <?php elseif (session()->getFlashdata('error')) : ?>
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian',
                    text: <?= json_encode(strip_tags((string) session()->getFlashdata('error'))) ?>,
                    timer: 3800,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                    timerProgressBar: true
                });
            }
        <?php endif; ?>
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const closeBtn = document.getElementById('sidebarCloseBtn');
        const sidebar = document.getElementById('appSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        function closeMobileSidebar() {
            if (sidebar) sidebar.classList.remove('sidebar-open');
            if (backdrop) backdrop.classList.remove('show');
        }

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('sidebar-open');
                if (backdrop) backdrop.classList.toggle('show');
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeMobileSidebar);
        }

        if (backdrop && sidebar) {
            backdrop.addEventListener('click', closeMobileSidebar);
        }

        // Global Handler: Pindahkan modal apapun langsung ke <body> saat hendak dibuka agar tidak tertutup backdrop / terperangkap stacking context .app-layout
        document.addEventListener('show.bs.modal', function(e) {
            if (e.target && e.target.parentElement !== document.body) {
                document.body.appendChild(e.target);
            }
        });

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
    <!-- Notification System Dynamic Handler -->
    <script src="<?= base_url('js/notification.js') ?>"></script>
    <!-- SweetAlert2 Library & SIAMA Centralized Notification Handler -->
    <script src="<?= base_url('js/sweetalert2.all.min.js') ?>"></script>
    <script src="<?= base_url('js/siama-swal.js?v=' . (file_exists(FCPATH . 'js/siama-swal.js') ? filemtime(FCPATH . 'js/siama-swal.js') : time())) ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
