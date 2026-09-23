<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', function() {
    return redirect()->to('/login');
});

// --- Rute Otentikasi ---
$routes->get('login', 'Auth::index');
$routes->post('login/process', 'Auth::process');
$routes->get('logout', 'Auth::logout');
$routes->get('change-password', 'Auth::changePassword');
$routes->post('change-password/process', 'Auth::processChangePassword');

// --- Rute Terproteksi (Hanya yang sudah login) ---
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// --- Rute Terproteksi dengan Role Khusus ---
// Contoh dummy rute untuk Admin_Pemkab
$routes->group('admin', ['filter' => ['auth', 'role:Admin_Pemkab']], static function ($routes) {
    $routes->get('dashboard', function() {
        return 'Selamat datang di Dashboard Admin Pemkab!';
    });
    $routes->get('users', function() {
        return 'Halaman Kelola Pengguna';
    });
});

// Contoh dummy rute untuk Pimpinan dan Kepala Bidang
$routes->group('approval', ['filter' => ['auth', 'role:Pimpinan,Kepala_Bidang']], static function ($routes) {
    $routes->get('berkas', function() {
        return 'Halaman Approval Berkas';
    });
});

// --- API Documentation (Swagger) ---
$routes->get('swagger', 'Swagger::index');
$routes->get('swagger/json', 'Swagger::json');

// --- Master OPD (Khusus Admin_Pemkab) ---
$routes->group('opd', ['filter' => ['auth', 'role:Admin_Pemkab']], static function ($routes) {
    $routes->get('/', 'Opd::index');
    $routes->get('create', 'Opd::create');
    $routes->post('store', 'Opd::store');
    $routes->get('edit/(:segment)', 'Opd::edit/$1');
    $routes->post('update/(:segment)', 'Opd::update/$1');
    $routes->get('delete/(:segment)', 'Opd::delete/$1');
});

// --- Master Bidang (Admin_Pemkab & Admin_OPD) ---
$routes->group('bidang', ['filter' => ['auth', 'role:Admin_Pemkab,Admin_OPD']], static function ($routes) {
    $routes->get('/', 'Bidang::index');
    $routes->get('create', 'Bidang::create');
    $routes->post('store', 'Bidang::store');
    $routes->get('edit/(:segment)', 'Bidang::edit/$1');
    $routes->post('update/(:segment)', 'Bidang::update/$1');
    $routes->get('delete/(:segment)', 'Bidang::delete/$1');
});

// --- Modul Master Kode Klasifikasi ---
$routes->group('kode-klasifikasi', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'KodeKlasifikasi::index');
    $routes->get('create', 'KodeKlasifikasi::create', ['filter' => 'role:Admin_Pemkab']);
    $routes->post('store', 'KodeKlasifikasi::store', ['filter' => 'role:Admin_Pemkab']);
    $routes->get('edit/(:segment)', 'KodeKlasifikasi::edit/$1', ['filter' => 'role:Admin_Pemkab']);
    $routes->post('update/(:segment)', 'KodeKlasifikasi::update/$1', ['filter' => 'role:Admin_Pemkab']);
    $routes->get('delete/(:segment)', 'KodeKlasifikasi::delete/$1', ['filter' => 'role:Admin_Pemkab']);
});

// --- Modul Master JRA ---
$routes->group('jra', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Jra::index', ['filter' => 'role:Admin_Pemkab,Admin_OPD']);
    $routes->get('create', 'Jra::create', ['filter' => 'role:Admin_Pemkab,Admin_OPD']);
    $routes->post('store', 'Jra::store', ['filter' => 'role:Admin_Pemkab,Admin_OPD']);
    $routes->get('edit/(:segment)', 'Jra::edit/$1', ['filter' => 'role:Admin_Pemkab,Admin_OPD']);
    $routes->post('update/(:segment)', 'Jra::update/$1', ['filter' => 'role:Admin_Pemkab,Admin_OPD']);
    $routes->get('delete/(:segment)', 'Jra::delete/$1', ['filter' => 'role:Admin_Pemkab,Admin_OPD']);
    // API untuk dropdown (Bisa diakses oleh Arsiparis saat create arsip)
    $routes->get('get_by_klasifikasi/(:segment)', 'Jra::get_by_klasifikasi/$1');
});

// --- Notifikasi ---
$routes->group('notification', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Notification::index');
    $routes->post('action/(:segment)', 'Notification::action/$1');
});

// --- Modul Berita Acara ---
$routes->group('berita-acara', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'BeritaAcara::index');
    $routes->get('create', 'BeritaAcara::create', ['filter' => 'role:Arsiparis']);
    $routes->post('store', 'BeritaAcara::store', ['filter' => 'role:Arsiparis']);
    $routes->get('edit/(:segment)', 'BeritaAcara::edit/$1', ['filter' => 'role:Arsiparis']);
    $routes->post('update/(:segment)', 'BeritaAcara::update/$1', ['filter' => 'role:Arsiparis']);
    $routes->get('delete/(:segment)', 'BeritaAcara::delete/$1', ['filter' => 'role:Arsiparis']);
    $routes->get('detail/(:segment)', 'BeritaAcara::detail/$1');
    $routes->post('verifikasi-kabid/(:segment)', 'BeritaAcara::verifikasiKabid/$1', ['filter' => 'role:Kepala_Bidang']);
    $routes->post('ttd-pimpinan/(:segment)', 'BeritaAcara::ttdPimpinan/$1', ['filter' => 'role:Pimpinan']);
    $routes->get('cetak/(:segment)', 'BeritaAcara::cetak/$1');
});

// --- Rute Penilaian Arsip (Hanya Admin Pemkab) ---
$routes->group('penilaian', ['filter' => 'role:Admin_Pemkab'], function($routes) {
    $routes->get('/', 'PenilaianArsip::index');
    $routes->get('form/(:segment)', 'PenilaianArsip::form/$1');
    $routes->post('store/(:segment)', 'PenilaianArsip::store/$1');
    $routes->get('detail/(:segment)', 'PenilaianArsip::detail/$1');
});

// --- Modul Surat Perintah Tugas (SPT) ---
$routes->group('spt', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Spt::index');
    $routes->get('create', 'Spt::create', ['filter' => 'role:Pimpinan']);
    $routes->post('store', 'Spt::store', ['filter' => 'role:Pimpinan']);
    $routes->get('detail/(:segment)', 'Spt::detail/$1');
    $routes->post('delete/(:segment)', 'Spt::delete/$1', ['filter' => 'role:Pimpinan']);
    
    // Penugasan Arsiparis oleh Kabid / Admin OPD
    $routes->get('assign/(:segment)', 'Spt::assign/$1', ['filter' => 'role:Kepala_Bidang,Admin_OPD']);
    $routes->post('process-assign/(:segment)', 'Spt::processAssign/$1', ['filter' => 'role:Kepala_Bidang,Admin_OPD']);
    
    // Mulai proses (Arsiparis)
    $routes->post('start-process/(:segment)', 'Spt::startProcess/$1', ['filter' => 'role:Arsiparis']);
});

// --- Modul Pengelolaan Arsip Digital ---
$routes->group('arsip', ['filter' => 'auth'], static function ($routes) {
    // Semua role yang sudah login dapat melihat daftar & detail arsip (filter role di Controller)
    $routes->get('/', 'Arsip::index');
    $routes->get('detail/(:segment)', 'Arsip::detail/$1');

    // Hanya Arsiparis (role: Arsiparis) yang boleh membuat, mengubah, dan menghapus arsip
    $routes->get('create', 'Arsip::create', ['filter' => 'role:Arsiparis']);
    $routes->post('store', 'Arsip::store', ['filter' => 'role:Arsiparis']);
    $routes->get('edit/(:segment)', 'Arsip::edit/$1', ['filter' => 'role:Arsiparis']);
    $routes->post('update/(:segment)', 'Arsip::update/$1', ['filter' => 'role:Arsiparis']);
    $routes->get('delete/(:segment)', 'Arsip::delete/$1', ['filter' => 'role:Arsiparis']);

    // Khusus Kepala Bidang: Verifikasi Arsip
    $routes->post('verify/(:segment)', 'Arsip::verify/$1', ['filter' => 'role:Kepala_Bidang']);
});

// --- Modul Manajemen Pengguna ---
$routes->group('users', ['filter' => ['auth', 'role:Admin_Pemkab,Admin_OPD']], static function ($routes) {
    $routes->get('/', 'UserController::index');
    $routes->get('create', 'UserController::create');
    $routes->post('store', 'UserController::store');
    $routes->get('edit/(:segment)', 'UserController::edit/$1');
    $routes->post('update/(:segment)', 'UserController::update/$1');
    $routes->get('reset-password/(:segment)', 'UserController::resetPassword/$1');
    $routes->get('delete/(:segment)', 'UserController::delete/$1');
});

// --- Modul Audit Log (Khusus Admin_Pemkab) ---
$routes->group('audit-log', ['filter' => ['auth', 'role:Admin_Pemkab']], static function ($routes) {
    $routes->get('access', 'AuditLog::accessLog');
    $routes->get('activity', 'AuditLog::activityLog');
});
