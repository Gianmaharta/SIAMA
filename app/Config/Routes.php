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
// Contoh dummy rute untuk Admin_Pemkab (id_role = 1)
$routes->group('admin', ['filter' => 'auth', 'filter' => 'role:1'], static function ($routes) {
    $routes->get('dashboard', function() {
        return 'Selamat datang di Dashboard Admin Pemkab!';
    });
    $routes->get('users', function() {
        return 'Halaman Kelola Pengguna';
    });
});

// Contoh dummy rute untuk Pimpinan (id_role = 2) dan Kepala Bidang (id_role = 4)
$routes->group('approval', ['filter' => ['auth', 'role:2,4']], static function ($routes) {
    $routes->get('berkas', function() {
        return 'Halaman Approval Berkas';
    });
});

// --- API Documentation (Swagger) ---
$routes->get('swagger', 'Swagger::index');
$routes->get('swagger/json', 'Swagger::json');

// --- Master OPD (Khusus Admin_Pemkab) ---
$routes->group('opd', ['filter' => ['auth', 'role:1']], static function ($routes) {
    $routes->get('/', 'Opd::index');
    $routes->get('create', 'Opd::create');
    $routes->post('store', 'Opd::store');
    $routes->get('edit/(:num)', 'Opd::edit/$1');
    $routes->post('update/(:num)', 'Opd::update/$1');
    $routes->get('delete/(:num)', 'Opd::delete/$1');
});

// --- Master Bidang (Admin_Pemkab & Admin_OPD) ---
$routes->group('bidang', ['filter' => ['auth', 'role:1,3']], static function ($routes) {
    $routes->get('/', 'Bidang::index');
    $routes->get('create', 'Bidang::create');
    $routes->post('store', 'Bidang::store');
    $routes->get('edit/(:num)', 'Bidang::edit/$1');
    $routes->post('update/(:num)', 'Bidang::update/$1');
    $routes->get('delete/(:num)', 'Bidang::delete/$1');
});

// --- Modul Surat Perintah Tugas (SPT) ---
$routes->group('spt', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Spt::index');
    $routes->get('create', 'Spt::create', ['filter' => 'role:2,3']);
    $routes->post('store', 'Spt::store', ['filter' => 'role:2,3']);
    $routes->get('detail/(:num)', 'Spt::detail/$1');
});

// --- Modul Pengelolaan Arsip Digital ---
$routes->group('arsip', ['filter' => 'auth'], static function ($routes) {
    // Semua role yang sudah login dapat melihat daftar & detail arsip (filter role di Controller)
    $routes->get('/', 'Arsip::index');
    $routes->get('detail/(:num)', 'Arsip::detail/$1');

    // Hanya Arsiparis (id_role: 5) yang boleh membuat, mengubah, dan menghapus arsip
    $routes->get('create', 'Arsip::create', ['filter' => 'role:5']);
    $routes->post('store', 'Arsip::store', ['filter' => 'role:5']);
    $routes->get('edit/(:num)', 'Arsip::edit/$1', ['filter' => 'role:5']);
    $routes->post('update/(:num)', 'Arsip::update/$1', ['filter' => 'role:5']);
    $routes->get('delete/(:num)', 'Arsip::delete/$1', ['filter' => 'role:5']);
});
