<?php

namespace App\Controllers;

use OpenApi\Attributes as OA;

/**
 * Dokumentasi OpenAPI/Swagger lengkap untuk seluruh endpoint SIAMA
 * agar seluruh rute pada app/Config/Routes.php tercakup dalam Swagger UI.
 */
class SwaggerExtraDocs
{
    // =========================================================================
    // 1. LANDING PAGE & PENGATURAN WEBSITE (Super_Admin)
    // =========================================================================

    #[OA\Get(
        path: '/',
        operationId: 'landingIndex',
        summary: 'Landing Page Publik SIAMA',
        description: 'Menampilkan halaman depan publik beserta statistik arsip, alur kerja, dan footer dinamis.',
        tags: ['Landing & Settings'],
        responses: [new OA\Response(response: 200, description: 'Halaman Landing Page berhasil ditampilkan')]
    )]
    public function landingIndex(): void {}

    #[OA\Get(
        path: '/settings',
        operationId: 'settingsIndex',
        summary: 'Halaman Pengaturan Website (Khusus Super_Admin)',
        description: 'Menampilkan konfigurasi branding, kontak footer, mekanisme password default, dan mode password.',
        tags: ['Landing & Settings'],
        security: [['sessionAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Data pengaturan website (HTML atau JSON dengan Accept: application/json)'),
            new OA\Response(response: 403, description: 'Akses ditolak selain Super_Admin'),
        ]
    )]
    public function settingsIndex(): void {}

    #[OA\Post(
        path: '/settings/update',
        operationId: 'settingsUpdate',
        summary: 'Simpan Pengaturan Website (Khusus Super_Admin)',
        tags: ['Landing & Settings'],
        security: [['sessionAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'site_name', type: 'string', example: 'SIAMA Kabupaten Buleleng'),
                        new OA\Property(property: 'site_tagline', type: 'string'),
                        new OA\Property(property: 'default_password', type: 'string', example: 'Admin123!'),
                        new OA\Property(property: 'password_mode', type: 'string', enum: ['default', 'random']),
                        new OA\Property(property: 'footer_address', type: 'string'),
                        new OA\Property(property: 'footer_phone', type: 'string'),
                        new OA\Property(property: 'footer_email', type: 'string'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Pengaturan berhasil diperbarui (JSON)'),
            new OA\Response(response: 302, description: 'Redirect ke /settings setelah sukses'),
        ]
    )]
    public function settingsUpdate(): void {}

    // =========================================================================
    // 2. AUTH TAMBAHAN
    // =========================================================================

    #[OA\Get(
        path: '/login',
        operationId: 'loginForm',
        summary: 'Halaman Form Login',
        tags: ['Auth'],
        responses: [new OA\Response(response: 200, description: 'Halaman login')]
    )]
    public function loginForm(): void {}

    #[OA\Get(
        path: '/change-password',
        operationId: 'changePasswordForm',
        summary: 'Halaman Ubah Password Wajib',
        description: 'Ditampilkan otomatis jika akun masih menggunakan password default atau baru di-reset.',
        tags: ['Auth'],
        security: [['sessionAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Halaman form ubah password')]
    )]
    public function changePasswordForm(): void {}

    #[OA\Post(
        path: '/change-password/process',
        operationId: 'changePasswordProcess',
        summary: 'Proses Simpan Password Baru',
        tags: ['Auth'],
        security: [['sessionAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    required: ['new_password', 'confirm_password'],
                    properties: [
                        new OA\Property(property: 'new_password', type: 'string', format: 'password'),
                        new OA\Property(property: 'confirm_password', type: 'string', format: 'password'),
                    ]
                )
            )
        ),
        responses: [new OA\Response(response: 302, description: 'Password diperbarui & redirect ke /dashboard')]
    )]
    public function changePasswordProcess(): void {}

    // =========================================================================
    // 3. DASHBOARD (ENDPOINT GRAFIK & WORKFLOW)
    // =========================================================================

    #[OA\Get(
        path: '/dashboard/chart-data',
        operationId: 'dashboardChartData',
        summary: 'Data Grafik Dashboard Utama (JSON)',
        tags: ['Dashboard'],
        security: [['sessionAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Data JSON grafik dashboard')]
    )]
    public function dashboardChartData(): void {}

    #[OA\Get(
        path: '/dashboard/kabid-chart-data',
        operationId: 'dashboardKabidChartData',
        summary: 'Data Grafik Dashboard Kepala Bidang (JSON)',
        tags: ['Dashboard'],
        security: [['sessionAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Data JSON grafik Kepala Bidang')]
    )]
    public function dashboardKabidChartData(): void {}

    #[OA\Get(
        path: '/dashboard/workflow-data',
        operationId: 'dashboardWorkflowData',
        summary: 'Data Progress Workflow Alih Media (JSON)',
        tags: ['Dashboard'],
        security: [['sessionAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Data JSON tahapan workflow alih media')]
    )]
    public function dashboardWorkflowData(): void {}

    // =========================================================================
    // 4. USERS (SERVER-SIDE DATATABLE, CRUD, RESET PASSWORD, TOGGLE, SWITCH)
    // =========================================================================

    #[OA\Get(
        path: '/users/datatable',
        operationId: 'usersDatatable',
        summary: 'Server-Side DataTables Manajemen Akun (JSON)',
        tags: ['Users'],
        security: [['sessionAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'draw', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'start', in: 'query', schema: new OA\Schema(type: 'integer', default: 0)),
            new OA\Parameter(name: 'length', in: 'query', schema: new OA\Schema(type: 'integer', default: 25)),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'role', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'id_opd', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'is_active', in: 'query', schema: new OA\Schema(type: 'integer', enum: [0, 1])),
        ],
        responses: [new OA\Response(response: 200, description: 'Payload DataTables Server-Side JSON')]
    )]
    public function usersDatatable(): void {}

    #[OA\Get(
        path: '/users/create',
        operationId: 'usersCreate',
        summary: 'Form Tambah Pengguna Baru',
        tags: ['Users'],
        security: [['sessionAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Halaman form tambah pengguna')]
    )]
    public function usersCreate(): void {}

    #[OA\Get(
        path: '/users/edit/{id}',
        operationId: 'usersEdit',
        summary: 'Form Edit Pengguna',
        tags: ['Users'],
        security: [['sessionAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 200, description: 'Halaman form edit pengguna')]
    )]
    public function usersEdit(): void {}

    #[OA\Post(
        path: '/users/update/{id}',
        operationId: 'usersUpdate',
        summary: 'Perbarui Data Pengguna',
        tags: ['Users'],
        security: [['sessionAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 302, description: 'Redirect ke /users setelah sukses')]
    )]
    public function usersUpdate(): void {}

    #[OA\Post(
        path: '/users/toggle-status/{id}',
        operationId: 'usersToggleStatus',
        summary: 'Toggle Aktif / Nonaktif Akun Pengguna',
        description: 'Mengubah status aktif/nonaktif pengguna langsung dari kolom Status tabel Manajemen Akun.',
        tags: ['Users'],
        security: [['sessionAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Status akun berhasil diubah (JSON)'),
            new OA\Response(response: 403, description: 'Tidak boleh menonaktifkan akun sendiri atau di luar hierarki'),
        ]
    )]
    public function usersToggleStatus(): void {}

    #[OA\Post(
        path: '/users/reset-password/{id}',
        operationId: 'usersResetPassword',
        summary: 'Reset Password Pengguna (Default / Random)',
        tags: ['Users'],
        security: [['sessionAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'mode', type: 'string', enum: ['default', 'random'], default: 'default'),
                    ]
                )
            )
        ),
        responses: [new OA\Response(response: 200, description: 'Password berhasil di-reset (JSON berisi new_password)')]
    )]
    public function usersResetPassword(): void {}

    #[OA\Post(
        path: '/users/delete/{id}',
        operationId: 'usersDelete',
        summary: 'Hapus Akun Pengguna',
        tags: ['Users'],
        security: [['sessionAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 302, description: 'Redirect ke /users setelah dihapus')]
    )]
    public function usersDelete(): void {}

    #[OA\Get(
        path: '/users/switch/{id}',
        operationId: 'usersSwitch',
        summary: 'Switch User / Masuk Sebagai (Khusus Super_Admin)',
        description: 'Mengalihkan sesi Super_Admin ke akun pengguna target untuk inspeksi.',
        tags: ['Users'],
        security: [['sessionAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Berhasil masuk sebagai user target (JSON)'),
            new OA\Response(response: 403, description: 'Ditolak selain Super_Admin asli'),
        ]
    )]
    public function usersSwitch(): void {}

    #[OA\Get(
        path: '/users/switch-back',
        operationId: 'usersSwitchBack',
        summary: 'Kembali ke Akun Super_Admin Asli',
        tags: ['Users'],
        security: [['sessionAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Sesi dikembalikan ke Super_Admin (JSON)')]
    )]
    public function usersSwitchBack(): void {}

    // =========================================================================
    // 5. MASTER OPD, BIDANG, KODE KLASIFIKASI & MASTER JRA
    // =========================================================================

    #[OA\Get(path: '/opd', summary: 'Daftar Master OPD', tags: ['Master OPD'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman daftar OPD')])]
    public function opdIndex(): void {}

    #[OA\Get(
        path: '/opd/datatable',
        operationId: 'opdDatatable',
        summary: 'Server-Side DataTables Master OPD (JSON)',
        tags: ['Master OPD'],
        security: [['sessionAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'draw', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'start', in: 'query', schema: new OA\Schema(type: 'integer', default: 0)),
            new OA\Parameter(name: 'length', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'is_active', in: 'query', schema: new OA\Schema(type: 'string', enum: ['all', '1', '0'])),
        ],
        responses: [new OA\Response(response: 200, description: 'Payload DataTables Master OPD JSON')]
    )]
    public function opdDatatable(): void {}

    #[OA\Post(path: '/opd/sync-api', operationId: 'opdSyncApi', summary: 'Sinkronisasi Master OPD dari API SIMPEG/SSO Pemkab Buleleng', description: 'Menarik seluruh OPD utama dari endpoint eksternal (/unit-kerja/utama) menggunakan Bearer Token di .env dan melakukan upsert ke tabel opd.', tags: ['Master OPD'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Statistik sinkronisasi OPD berhasil (JSON)'), new OA\Response(response: 500, description: 'Gagal terhubung ke API eksternal')])]
    public function opdSyncApi(): void {}

    #[OA\Get(path: '/opd/create', summary: 'Form Tambah OPD', tags: ['Master OPD'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman tambah OPD')])]
    public function opdCreate(): void {}

    #[OA\Post(path: '/opd/store', summary: 'Simpan OPD Baru', tags: ['Master OPD'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function opdStore(): void {}

    #[OA\Get(path: '/opd/edit/{id}', summary: 'Form Edit OPD', tags: ['Master OPD'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Halaman edit OPD')])]
    public function opdEdit(): void {}

    #[OA\Post(path: '/opd/update/{id}', summary: 'Perbarui Data OPD', tags: ['Master OPD'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function opdUpdate(): void {}

    #[OA\Post(path: '/opd/delete/{id}', summary: 'Hapus OPD', tags: ['Master OPD'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah dihapus')])]
    public function opdDelete(): void {}

    #[OA\Get(path: '/bidang', summary: 'Daftar Master Bidang', tags: ['Master Bidang'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman daftar Bidang')])]
    public function bidangIndex(): void {}

    #[OA\Get(
        path: '/bidang/datatable',
        operationId: 'bidangDatatable',
        summary: 'Server-Side DataTables Master Bidang (JSON)',
        tags: ['Master Bidang'],
        security: [['sessionAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'draw', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'start', in: 'query', schema: new OA\Schema(type: 'integer', default: 0)),
            new OA\Parameter(name: 'length', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_by', in: 'query', schema: new OA\Schema(type: 'string', enum: ['id_bidang', 'nama_opd', 'nama_bidang'])),
            new OA\Parameter(name: 'sort_dir', in: 'query', schema: new OA\Schema(type: 'string', enum: ['ASC', 'DESC'])),
        ],
        responses: [new OA\Response(response: 200, description: 'Payload DataTables Master Bidang JSON')]
    )]
    public function bidangDatatable(): void {}

    #[OA\Post(
        path: '/bidang/sync-api',
        operationId: 'bidangSyncApi',
        summary: 'Sinkronisasi Master Bidang dari API SIMPEG/SSO Pemkab Buleleng',
        description: 'Menarik seluruh bidang aktif per OPD dari endpoint eksternal (/unit-kerja/{kode}/bidang) menggunakan Bearer Token di .env.',
        tags: ['Master Bidang'],
        security: [['sessionAuth' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'id_opd', type: 'string', description: 'Opsional UUID OPD tertentu; kosongkan untuk sinkronisasi seluruh OPD'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Statistik sinkronisasi Bidang berhasil (JSON)'),
            new OA\Response(response: 500, description: 'Gagal terhubung ke API eksternal'),
        ]
    )]
    public function bidangSyncApi(): void {}

    #[OA\Get(path: '/bidang/create', summary: 'Form Tambah Bidang', tags: ['Master Bidang'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman tambah Bidang')])]
    public function bidangCreate(): void {}

    #[OA\Post(path: '/bidang/store', summary: 'Simpan Bidang Baru', tags: ['Master Bidang'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function bidangStore(): void {}

    #[OA\Get(path: '/bidang/edit/{id}', summary: 'Form Edit Bidang', tags: ['Master Bidang'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Halaman edit Bidang')])]
    public function bidangEdit(): void {}

    #[OA\Post(path: '/bidang/update/{id}', summary: 'Perbarui Data Bidang', tags: ['Master Bidang'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function bidangUpdate(): void {}

    #[OA\Post(path: '/bidang/delete/{id}', summary: 'Hapus Bidang', tags: ['Master Bidang'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah dihapus')])]
    public function bidangDelete(): void {}

    #[OA\Get(path: '/kode-klasifikasi', summary: 'Daftar Kode Klasifikasi', tags: ['Kode Klasifikasi'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman daftar kode klasifikasi')])]
    public function kodeIndex(): void {}

    #[OA\Get(path: '/kode-klasifikasi/create', summary: 'Form Tambah Kode Klasifikasi', tags: ['Kode Klasifikasi'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman tambah kode klasifikasi')])]
    public function kodeCreate(): void {}

    #[OA\Post(path: '/kode-klasifikasi/store', summary: 'Simpan Kode Klasifikasi', tags: ['Kode Klasifikasi'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function kodeStore(): void {}

    #[OA\Get(path: '/kode-klasifikasi/edit/{id}', summary: 'Form Edit Kode Klasifikasi', tags: ['Kode Klasifikasi'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Halaman edit kode klasifikasi')])]
    public function kodeEdit(): void {}

    #[OA\Post(path: '/kode-klasifikasi/update/{id}', summary: 'Perbarui Kode Klasifikasi', tags: ['Kode Klasifikasi'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function kodeUpdate(): void {}

    #[OA\Get(path: '/kode-klasifikasi/delete/{id}', summary: 'Hapus Kode Klasifikasi', tags: ['Kode Klasifikasi'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah dihapus')])]
    public function kodeDelete(): void {}

    #[OA\Get(path: '/jra', summary: 'Daftar Jadwal Retensi Arsip (JRA)', tags: ['Master JRA'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman daftar JRA (25 baris awal / JSON)')])]
    public function jraIndex(): void {}

    #[OA\Get(
        path: '/jra/datatable',
        operationId: 'jraDatatable',
        summary: 'Server-Side DataTables Master JRA (JSON)',
        description: 'Mengambil data Master JRA (2.444+ baris) secara bertahap dengan pagination, search, dan sorting di server.',
        tags: ['Master JRA'],
        security: [['sessionAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'draw', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'start', in: 'query', schema: new OA\Schema(type: 'integer', default: 0)),
            new OA\Parameter(name: 'length', in: 'query', schema: new OA\Schema(type: 'integer', default: 25)),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_by', in: 'query', schema: new OA\Schema(type: 'string', enum: ['kode', 'retensi_aktif', 'retensi_inaktif', 'keterangan', 'keamanan', 'dasar'])),
            new OA\Parameter(name: 'sort_dir', in: 'query', schema: new OA\Schema(type: 'string', enum: ['ASC', 'DESC'])),
        ],
        responses: [new OA\Response(response: 200, description: 'Payload DataTables Server-Side JSON')]
    )]
    public function jraDatatable(): void {}

    #[OA\Get(path: '/jra/create', summary: 'Form Tambah JRA', tags: ['Master JRA'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman tambah JRA')])]
    public function jraCreate(): void {}

    #[OA\Post(path: '/jra/store', summary: 'Simpan JRA Baru', tags: ['Master JRA'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function jraStore(): void {}

    #[OA\Get(path: '/jra/edit/{id}', summary: 'Form Edit JRA', tags: ['Master JRA'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Halaman edit JRA')])]
    public function jraEdit(): void {}

    #[OA\Post(path: '/jra/update/{id}', summary: 'Perbarui Data JRA', tags: ['Master JRA'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function jraUpdate(): void {}

    #[OA\Post(path: '/jra/delete/{id}', summary: 'Hapus Data JRA', tags: ['Master JRA'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah dihapus')])]
    public function jraDelete(): void {}

    #[OA\Get(
        path: '/jra/get_by_klasifikasi/{id}',
        summary: 'Ambil JRA berdasarkan Klasifikasi (AJAX JSON)',
        tags: ['Master JRA'],
        security: [['sessionAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 200, description: 'Data JSON JRA')]
    )]
    public function get_by_klasifikasi(): void {}

    // =========================================================================
    // 6. NOTIFIKASI
    // =========================================================================

    #[OA\Get(path: '/notification', summary: 'Daftar Notifikasi', tags: ['Notifikasi'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman daftar notifikasi')])]
    public function notifIndex(): void {}

    #[OA\Get(path: '/notification/unread-count', summary: 'Jumlah Notifikasi Belum Dibaca (JSON)', tags: ['Notifikasi'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Jumlah unread notification')])]
    public function notifUnreadCount(): void {}

    #[OA\Get(path: '/notification/dropdown', summary: 'Daftar Notifikasi Dropdown Header (JSON)', tags: ['Notifikasi'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'List dropdown notifikasi')])]
    public function notifDropdown(): void {}

    #[OA\Get(path: '/notification/feed', summary: 'Feed Notifikasi Terbaru (JSON)', tags: ['Notifikasi'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Feed notifikasi')])]
    public function notifFeed(): void {}

    #[OA\Get(path: '/notification/read-and-redirect/{id}', summary: 'Tandai Dibaca & Redirect ke Target', tags: ['Notifikasi'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect ke halaman target notifikasi')])]
    public function notifReadAndRedirect(): void {}

    #[OA\Post(path: '/notification/mark-read/{id}', summary: 'Tandai Satu Notifikasi Dibaca', tags: ['Notifikasi'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Status sukses JSON')])]
    public function notifMarkRead(): void {}

    #[OA\Post(path: '/notification/mark-all-read', summary: 'Tandai Seluruh Notifikasi Dibaca', tags: ['Notifikasi'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Status sukses JSON')])]
    public function notifMarkAllRead(): void {}

    #[OA\Post(
        path: '/notification/action/{id}',
        summary: 'Aksi Notifikasi (Tandai Dibaca/Musnahkan)',
        tags: ['Notifikasi'],
        security: [['sessionAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')]
    )]
    public function notifAction(): void {}

    // =========================================================================
    // 7. BERITA ACARA & PENILAIAN ARSIP
    // =========================================================================

    #[OA\Get(path: '/berita-acara/create', summary: 'Form Tambah Berita Acara (Arsiparis)', tags: ['Berita Acara'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman tambah Berita Acara')])]
    public function baCreate(): void {}

    #[OA\Post(path: '/berita-acara/store', summary: 'Simpan Berita Acara Baru (Arsiparis)', tags: ['Berita Acara'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function baStore(): void {}

    #[OA\Get(path: '/berita-acara/detail/{id}', summary: 'Detail Berita Acara', tags: ['Berita Acara'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Halaman detail Berita Acara')])]
    public function baDetail(): void {}

    #[OA\Get(path: '/berita-acara/edit/{id}', summary: 'Form Edit Berita Acara (Arsiparis)', tags: ['Berita Acara'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Halaman edit Berita Acara')])]
    public function baEdit(): void {}

    #[OA\Post(path: '/berita-acara/update/{id}', summary: 'Perbarui Berita Acara (Arsiparis)', tags: ['Berita Acara'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function baUpdate(): void {}

    #[OA\Get(path: '/berita-acara/delete/{id}', summary: 'Hapus Berita Acara (Arsiparis)', tags: ['Berita Acara'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah dihapus')])]
    public function baDelete(): void {}

    #[OA\Post(path: '/berita-acara/verifikasi-kabid/{id}', summary: 'Verifikasi Berita Acara oleh Kepala Bidang', tags: ['Berita Acara'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah verifikasi')])]
    public function baVerifikasiKabid(): void {}

    #[OA\Post(path: '/berita-acara/ttd-pimpinan/{id}', summary: 'Pengesahan/TTD Berita Acara oleh Pimpinan', tags: ['Berita Acara'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah disahkan')])]
    public function baTtdPimpinan(): void {}

    #[OA\Get(path: '/berita-acara/cetak/{id}', summary: 'Cetak PDF Berita Acara', tags: ['Berita Acara'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Dokumen PDF Berita Acara')])]
    public function baCetak(): void {}

    #[OA\Get(path: '/penilaian', summary: 'Daftar Penilaian Arsip', tags: ['Penilaian Arsip'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman daftar penilaian')])]
    public function penIndex(): void {}

    #[OA\Get(
        path: '/penilaian/form/{id}',
        summary: 'Formulir Penilaian Arsip',
        tags: ['Penilaian Arsip'],
        security: [['sessionAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 200, description: 'Halaman form penilaian')]
    )]
    public function penForm(): void {}

    #[OA\Post(
        path: '/penilaian/store/{id}',
        summary: 'Simpan Penilaian Arsip',
        tags: ['Penilaian Arsip'],
        security: [['sessionAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses menyimpan')]
    )]
    public function penStore(): void {}

    #[OA\Get(path: '/penilaian/detail/{id}', summary: 'Detail Hasil Penilaian Arsip', tags: ['Penilaian Arsip'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Halaman detail penilaian arsip')])]
    public function penDetail(): void {}

    // =========================================================================
    // 8. SURAT PERINTAH TUGAS (SPT) & ARSIP DIGITAL
    // =========================================================================

    #[OA\Get(path: '/spt', summary: 'Daftar Surat Perintah Tugas (SPT)', tags: ['Surat Perintah Tugas (SPT)'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman daftar SPT')])]
    public function sptIndex(): void {}

    #[OA\Get(path: '/spt/create', summary: 'Form Tambah SPT (Pimpinan)', tags: ['Surat Perintah Tugas (SPT)'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman tambah SPT')])]
    public function sptCreate(): void {}

    #[OA\Post(path: '/spt/store', summary: 'Upload SPT Baru', tags: ['Surat Perintah Tugas (SPT)'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function sptStore(): void {}

    #[OA\Get(path: '/spt/detail/{id}', summary: 'Detail SPT', tags: ['Surat Perintah Tugas (SPT)'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Halaman detail SPT')])]
    public function sptDetail(): void {}

    #[OA\Post(path: '/spt/delete/{id}', summary: 'Hapus SPT (Pimpinan)', tags: ['Surat Perintah Tugas (SPT)'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah dihapus')])]
    public function sptDelete(): void {}

    #[OA\Get(path: '/spt/assign/{id}', summary: 'Form Penugasan Arsiparis pada SPT (Kepala Bidang)', tags: ['Surat Perintah Tugas (SPT)'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Halaman penugasan SPT')])]
    public function sptAssign(): void {}

    #[OA\Post(
        path: '/spt/process-assign/{id}',
        summary: 'Penugasan Arsiparis pada SPT',
        tags: ['Surat Perintah Tugas (SPT)'],
        security: [['sessionAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 302, description: 'Redirect setelah menugaskan')]
    )]
    public function processAssign(): void {}

    #[OA\Post(path: '/spt/start-process/{id}', summary: 'Mulai Proses Pengerjaan SPT (Arsiparis)', tags: ['Surat Perintah Tugas (SPT)'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah mulai kerja')])]
    public function sptStartProcess(): void {}

    #[OA\Get(
        path: '/arsip/datatable',
        operationId: 'arsipDatatable',
        summary: 'Server-Side DataTables Pengelolaan Arsip (JSON)',
        tags: ['Arsip'],
        security: [['sessionAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'draw', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'start', in: 'query', schema: new OA\Schema(type: 'integer', default: 0)),
            new OA\Parameter(name: 'length', in: 'query', schema: new OA\Schema(type: 'integer', default: 25)),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'status_verifikasi', in: 'query', schema: new OA\Schema(type: 'string', enum: ['Draft', 'Menunggu', 'Selesai'])),
            new OA\Parameter(name: 'id_bidang', in: 'query', schema: new OA\Schema(type: 'string')),
        ],
        responses: [new OA\Response(response: 200, description: 'Payload DataTables Server-Side JSON')]
    )]
    public function arsipDatatable(): void {}

    #[OA\Get(path: '/arsip/detail/{id}', summary: 'Detail Arsip Digital', tags: ['Arsip'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Halaman detail arsip')])]
    public function arsipDetail(): void {}

    #[OA\Get(path: '/arsip/create', summary: 'Form Tambah Arsip (Arsiparis)', tags: ['Arsip'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman tambah arsip')])]
    public function arsipCreate(): void {}

    #[OA\Get(path: '/arsip/edit/{id}', summary: 'Form Edit Arsip (Arsiparis)', tags: ['Arsip'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Halaman edit arsip')])]
    public function arsipEdit(): void {}

    #[OA\Post(path: '/arsip/update/{id}', summary: 'Perbarui Data Arsip (Arsiparis)', tags: ['Arsip'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function arsipUpdate(): void {}

    #[OA\Get(path: '/arsip/delete/{id}', summary: 'Hapus Arsip (Arsiparis)', tags: ['Arsip'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah dihapus')])]
    public function arsipDelete(): void {}

    #[OA\Post(path: '/arsip/verify/{id}', summary: 'Verifikasi Arsip (Kepala Bidang / Admin)', tags: ['Arsip'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 302, description: 'Redirect setelah verifikasi')])]
    public function arsipVerify(): void {}

    #[OA\Get(path: '/uploads/arsip/{filename}', summary: 'Streaming File Digital Arsip', tags: ['Arsip'], parameters: [new OA\Parameter(name: 'filename', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Stream berkas PDF/Gambar arsip')])]
    public function serveArsipFile(): void {}

    #[OA\Get(path: '/uploads/spt/{filename}', summary: 'Streaming File Digital SPT', tags: ['Surat Perintah Tugas (SPT)'], parameters: [new OA\Parameter(name: 'filename', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Stream berkas SPT')])]
    public function serveSptFile(): void {}

    // =========================================================================
    // 9. AUDIT LOG & SYSTEM MAINTENANCE
    // =========================================================================

    #[OA\Get(path: '/audit-log/access', summary: 'Log Akses (Login/Logout)', tags: ['Audit Log'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman Access Log')])]
    public function accessLog(): void {}

    #[OA\Get(
        path: '/audit-log/access/datatable',
        operationId: 'accessLogDatatable',
        summary: 'Server-Side DataTables Access Log (JSON)',
        tags: ['Audit Log'],
        security: [['sessionAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'draw', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'start', in: 'query', schema: new OA\Schema(type: 'integer', default: 0)),
            new OA\Parameter(name: 'length', in: 'query', schema: new OA\Schema(type: 'integer', default: 25)),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'action', in: 'query', schema: new OA\Schema(type: 'string')),
        ],
        responses: [new OA\Response(response: 200, description: 'Payload DataTables Access Log JSON')]
    )]
    public function accessLogDatatable(): void {}

    #[OA\Get(path: '/audit-log/access/export', summary: 'Ekspor Access Log ke CSV', tags: ['Audit Log'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'File CSV Access Log')])]
    public function accessLogExport(): void {}

    #[OA\Get(path: '/audit-log/activity', summary: 'Log Aktivitas Sistem (CRUD & Grafik)', tags: ['Audit Log'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman Activity Log / JSON dengan Accept: application/json')])]
    public function activityLog(): void {}

    #[OA\Get(
        path: '/audit-log/activity/chart-data',
        operationId: 'activityLogChartData',
        summary: 'Data Grafik Activity Log (JSON)',
        description: 'Mengembalikan tren harian, distribusi per modul, dan distribusi per aksi untuk grafik Chart.js.',
        tags: ['Audit Log'],
        security: [['sessionAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'periode', in: 'query', schema: new OA\Schema(type: 'string', enum: ['today', 'week', 'month', 'year'])),
            new OA\Parameter(name: 'module', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'start_date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'end_date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
        ],
        responses: [new OA\Response(response: 200, description: 'Data JSON grafik Activity Log')]
    )]
    public function activityLogChartData(): void {}

    #[OA\Get(
        path: '/audit-log/activity/datatable',
        operationId: 'activityLogDatatable',
        summary: 'Server-Side DataTables Activity Log (JSON)',
        tags: ['Audit Log'],
        security: [['sessionAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'draw', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'start', in: 'query', schema: new OA\Schema(type: 'integer', default: 0)),
            new OA\Parameter(name: 'length', in: 'query', schema: new OA\Schema(type: 'integer', default: 25)),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'module', in: 'query', schema: new OA\Schema(type: 'string')),
        ],
        responses: [new OA\Response(response: 200, description: 'Payload DataTables Activity Log JSON')]
    )]
    public function activityLogDatatable(): void {}

    #[OA\Get(path: '/audit-log/activity/export', summary: 'Ekspor Activity Log ke CSV', tags: ['Audit Log'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'File CSV Activity Log')])]
    public function activityLogExport(): void {}

    #[OA\Get(path: '/system-cleanse', summary: 'Halaman Reset Data / Data Cleansing', tags: ['System Maintenance'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman Data Cleansing')])]
    public function systemCleanseIndex(): void {}

    #[OA\Post(path: '/system-cleanse/execute', summary: 'Eksekusi Reset / Pembersihan Data', tags: ['System Maintenance'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 302, description: 'Redirect setelah proses selesai')])]
    public function systemCleanseExecute(): void {}

    #[OA\Post(path: '/system-cleanse/backup', operationId: 'systemBackupCreate', summary: 'Buat Backup Database (.SQL) Manual (Khusus Super_Admin)', tags: ['System Maintenance'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Backup .sql berhasil dibuat tanpa menghapus data (JSON)')])]
    public function systemBackupCreate(): void {}

    #[OA\Get(path: '/system-cleanse/backup-download/{folder}', operationId: 'systemBackupDownload', summary: 'Download File Backup Database .SQL (Khusus Super_Admin)', tags: ['System Maintenance'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'folder', in: 'path', required: true, schema: new OA\Schema(type: 'string', example: 'backup_20261125_073500'))], responses: [new OA\Response(response: 200, description: 'Stream file .sql hasil backup')])]
    public function systemBackupDownload(): void {}

    #[OA\Post(path: '/system-cleanse/backup-delete/{folder}', operationId: 'systemBackupDelete', summary: 'Hapus File Backup Database (Khusus Super_Admin)', tags: ['System Maintenance'], security: [['sessionAuth' => []]], parameters: [new OA\Parameter(name: 'folder', in: 'path', required: true, schema: new OA\Schema(type: 'string', example: 'backup_20261125_073500'))], responses: [new OA\Response(response: 200, description: 'Backup berhasil dihapus')])]
    public function systemBackupDelete(): void {}
}
