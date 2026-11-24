<?php

namespace Tests\App\Feature;

use CodeIgniter\Test\CIUnitTestCase;

class SweetAlertIntegrationTest extends CIUnitTestCase
{
    /**
     * Pastikan seluruh asset SweetAlert2 tersedia di direktori public
     */
    public function testSweetAlertAssetsExistOnDisk(): void
    {
        $swalBundle = ROOTPATH . 'public/js/sweetalert2.all.min.js';
        $swalCss    = ROOTPATH . 'public/css/siama-swal.css';
        $swalJs     = ROOTPATH . 'public/js/siama-swal.js';

        $this->assertFileExists($swalBundle, 'File sweetalert2.all.min.js harus tersedia di public/js/');
        $this->assertGreaterThan(50000, filesize($swalBundle), 'Bundle SweetAlert2 harus berukuran lengkap.');

        $this->assertFileExists($swalCss, 'File siama-swal.css harus tersedia di public/css/');
        $this->assertFileExists($swalJs, 'File siama-swal.js harus tersedia di public/js/');

        $cssContent = file_get_contents($swalCss);
        $this->assertStringContainsString('#1E60EC', $cssContent, 'CSS harus memuat Primary Color #1E60EC.');
        $this->assertStringContainsString('#0B1528', $cssContent, 'CSS harus memuat Dark Navy #0B1528.');
        $this->assertStringContainsString('Poppins', $cssContent, 'CSS harus menggunakan font Poppins.');
        $this->assertStringContainsString('2050', $cssContent, 'CSS z-index harus 2050 agar di atas backdrop.');

        $jsContent = file_get_contents($swalJs);
        $this->assertStringContainsString('SiamaSwal', $jsContent, 'JS harus mengekspos window.SiamaSwal.');
        $this->assertStringContainsString('confirmDelete', $jsContent, 'JS harus menyediakan method confirmDelete.');
        $this->assertStringContainsString('data-swal-confirm', $jsContent, 'JS harus meng-handle data-swal-confirm.');
    }

    /**
     * Pastikan layout utama memuat CSS, JS SweetAlert2, dan DOM container flashdata
     */
    public function testLayoutIncludesSweetAlertAssetsAndContainer(): void
    {
        $layoutFile = APPPATH . 'Views/layouts/main.php';
        $this->assertFileExists($layoutFile);
        $content = file_get_contents($layoutFile);

        $this->assertStringContainsString('siama-swal.css', $content);
        $this->assertStringContainsString('sweetalert2.all.min.js', $content);
        $this->assertStringContainsString('siama-swal.js', $content);
        $this->assertStringContainsString('id="siama-flash-data"', $content);
        $this->assertStringContainsString("session()->getFlashdata('success')", $content);
        $this->assertStringContainsString("session()->getFlashdata('error')", $content);
    }

    /**
     * Pastikan halaman login juga memuat SweetAlert2 dan DOM container flashdata
     */
    public function testLoginPageIncludesSweetAlertAssetsAndContainer(): void
    {
        $loginFile = APPPATH . 'Views/auth/login.php';
        $this->assertFileExists($loginFile);
        $content = file_get_contents($loginFile);

        $this->assertStringContainsString('siama-swal.css', $content);
        $this->assertStringContainsString('sweetalert2.all.min.js', $content);
        $this->assertStringContainsString('siama-swal.js', $content);
        $this->assertStringContainsString('id="siama-flash-data"', $content);
    }

    /**
     * Pastikan views tidak lagi memuat inline bootstrap alert banners untuk flashdata
     */
    public function testViewsHaveNoInlineFlashAlertBanners(): void
    {
        $viewsToCheck = [
            'penilaian/index.php',
            'penilaian/form.php',
            'spt/create.php',
            'spt/assign.php',
            'spt/detail.php',
            'spt/index.php',
            'dashboard/index.php',
            'notification/index.php',
            'auth/change_password.php',
            'user/create.php',
            'user/edit.php',
            'user/index.php',
            'opd/create.php',
            'opd/edit.php',
            'opd/index.php',
            'bidang/create.php',
            'bidang/edit.php',
            'bidang/index.php',
            'jra/create.php',
            'jra/edit.php',
            'arsip/create.php',
            'arsip/edit.php',
            'arsip/detail.php',
            'arsip/index.php',
            'berita_acara/create.php',
            'berita_acara/edit.php',
            'berita_acara/detail.php',
            'berita_acara/index.php',
            'system/cleanse.php',
        ];

        foreach ($viewsToCheck as $viewRelPath) {
            $path = APPPATH . 'Views/' . $viewRelPath;
            $this->assertFileExists($path, "File view $viewRelPath harus ada.");
            $content = file_get_contents($path);

            $this->assertStringNotContainsString(
                'alert alert-success alert-dismissible',
                $content,
                "View $viewRelPath tidak boleh memiliki alert-success dismissible banner bawaan."
            );
            $this->assertStringNotContainsString(
                'alert alert-danger alert-dismissible',
                $content,
                "View $viewRelPath tidak boleh memiliki alert-danger dismissible banner bawaan."
            );
        }
    }

    /**
     * Pastikan tidak ada dialog confirm() native JavaScript di seluruh views
     */
    public function testNoNativeConfirmInViews(): void
    {
        $finder = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(APPPATH . 'Views'));
        foreach ($finder as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getRealPath());
                $this->assertDoesNotMatchRegularExpression(
                    '/onsubmit=["\'].*confirm\(.*["\']/',
                    $content,
                    'Tidak boleh ada onsubmit="return confirm(...)" di ' . $file->getFilename()
                );
            }
        }
    }

    /**
     * Pastikan tidak ada modal konfirmasi hapus ganda (Bootstrap modal modalHapus)
     * dan tombol hapus tidak memiliki atribut data-bs-toggle="modal"
     */
    public function testNoDuplicateDeleteModalsOrBootstrapTriggersOnDeleteButtons(): void
    {
        $finder = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(APPPATH . 'Views'));
        foreach ($finder as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getRealPath());
                $this->assertStringNotContainsString(
                    'modalHapus',
                    $content,
                    'Tidak boleh ada elemen atau referensi modalHapus di ' . $file->getFilename()
                );
                $this->assertDoesNotMatchRegularExpression(
                    '/class=["\'][^"\']*btn-(?:action-)?delete[^"\']*["\'][^>]*data-bs-toggle=["\']modal["\']/',
                    $content,
                    'Tombol hapus tidak boleh memiliki data-bs-toggle="modal" di ' . $file->getFilename()
                );
                $this->assertDoesNotMatchRegularExpression(
                    '/data-bs-toggle=["\']modal["\'][^>]*class=["\'][^"\']*btn-(?:action-)?delete/',
                    $content,
                    'Tombol hapus tidak boleh memiliki data-bs-toggle="modal" di ' . $file->getFilename()
                );
            }
        }
    }

    /**
     * Pastikan tidak ada popup loading kedua (SiamaSwal.loading) pada proses konfirmasi hapus data
     */
    public function testDeleteConfirmationHasNoSecondaryLoadingPopup(): void
    {
        $swalJs = ROOTPATH . 'public/js/siama-swal.js';
        $this->assertFileExists($swalJs);
        $content = file_get_contents($swalJs);

        // Tidak boleh ada pemanggilan SiamaSwal.loading() pada konfirmasi aksi
        $this->assertStringNotContainsString(
            "SiamaSwal.loading('Menghapus",
            $content,
            'Tidak boleh ada pemanggilan popup kedua SiamaSwal.loading pada proses hapus data'
        );

        // Harus menggunakan setSwalButtonLoading untuk loading in-button
        $this->assertStringContainsString('setSwalButtonLoading', $content);
    }

    /**
     * Pastikan pembuatan akun dan reset password tidak menampilkan password default secara terbuka di flash message
     */
    public function testUserCreationFlashMessageDoesNotExposeDefaultPassword(): void
    {
        $controllerFile = APPPATH . 'Controllers/UserController.php';
        $this->assertFileExists($controllerFile);
        $content = file_get_contents($controllerFile);

        // Flash message tidak boleh mengandung "password default: Admin123!"
        $this->assertStringNotContainsString(
            "with('success', 'Akun berhasil dibuat dengan password default",
            $content,
            'Pesan sukses pembuatan akun tidak boleh mengekspos password default.'
        );

        $this->assertStringContainsString(
            "with('success', 'Akun pengguna baru berhasil dibuat.')",
            $content,
            'Pesan sukses pembuatan akun harus informatif dan aman.'
        );
    }

    /**
     * Pastikan konfigurasi SweetAlert2 Success sesuai ketentuan:
     * - Auto close dengan timer ~3 detik (3000ms)
     * - showConfirmButton: false, showCancelButton: false, showDenyButton: false
     * - Menutup dialog aktif sebelumnya untuk mencegah popup ganda
     */
    public function testSweetAlertSuccessNotificationConfiguration(): void
    {
        $swalJs = ROOTPATH . 'public/js/siama-swal.js';
        $content = file_get_contents($swalJs);

        $this->assertStringContainsString('timer = 3000', $content, 'Default timer success harus 3000ms (3 detik).');
        $this->assertStringContainsString('showConfirmButton: false', $content);
        $this->assertStringContainsString('showCancelButton: false', $content);
        $this->assertStringContainsString('showDenyButton: false', $content);
    }

    /**
     * Pastikan corner radius dan jarak antar tombol konfirmasi (gap / margin) telah ditingkatkan
     */
    public function testSweetAlertCornerRadiusAndButtonSpacing(): void
    {
        $swalCss = ROOTPATH . 'public/css/siama-swal.css';
        $content = file_get_contents($swalCss);

        // Corner radius popup dan detail box
        $this->assertStringContainsString('border-radius: 28px', $content, 'Border radius popup harus minimal 28px.');
        $this->assertStringContainsString('border-radius: 18px', $content, 'Border radius detail box harus minimal 18px.');
        $this->assertStringContainsString('border-radius: 14px', $content, 'Border radius tombol harus minimal 14px.');

        // Spasi dan margin tombol aksi agar tidak saling menempel
        $this->assertStringContainsString('gap: 16px', $content, 'Actions container harus memiliki gap 16px.');
        $this->assertStringContainsString('margin: 0 8px', $content, 'Tombol aksi harus memiliki margin horizontal.');
        $this->assertStringContainsString('margin-left: 16px', $content, 'Tombol adjacent harus memiliki margin pemisah 16px.');

        // JS mixin juga harus memiliki didOpen enforcing
        $swalJs = ROOTPATH . 'public/js/siama-swal.js';
        $jsContent = file_get_contents($swalJs);
        $this->assertStringContainsString('didOpen: (popup)', $jsContent, 'JS Mixin harus memiliki didOpen handler pemisah tombol.');
    }

    /**
     * Pastikan tombol Deny / "No" dinonaktifkan sepenuhnya sehingga hanya ada 2 tombol (Batal & Hapus)
     */
    public function testSweetAlertDenyButtonIsDisabledAndOnlyTwoButtonsInConfirmation(): void
    {
        $swalJs = ROOTPATH . 'public/js/siama-swal.js';
        $jsContent = file_get_contents($swalJs);

        $this->assertStringContainsString('showDenyButton: false', $jsContent, 'showDenyButton harus false di JS config.');
        $this->assertStringContainsString('denyBtn.remove()', $jsContent, 'Deny button harus dihapus dari DOM jika ada.');

        $swalCss = ROOTPATH . 'public/css/siama-swal.css';
        $cssContent = file_get_contents($swalCss);

        $this->assertStringContainsString('.swal2-actions .swal2-deny', $cssContent);
        $this->assertStringContainsString('display: none !important', $cssContent, 'CSS harus memaksa deny button display none.');
    }
}
