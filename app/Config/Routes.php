<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// --- Rute Otentikasi ---
$routes->get('login', 'Auth::index');
$routes->post('login/process', 'Auth::process');
$routes->get('logout', 'Auth::logout');

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
