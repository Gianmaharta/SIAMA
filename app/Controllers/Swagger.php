<?php

namespace App\Controllers;

use OpenApi\Generator;
use OpenApi\Attributes as OA;

class Swagger extends BaseController
{
    public function index()
    {
        return view('swagger/index');
    }

    public function json()
    {
        // Scan direktori Controllers untuk anotasi PHP 8 Attributes OpenAPI
        $generator = new Generator();

        // validate: false => Mencegah trigger_error dari CI4 saat anotasi belum lengkap 100%
        $openapi = $generator->generate([APPPATH . 'Controllers'], validate: false);

        if ($openapi === null) {
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setBody(json_encode(['error' => 'Gagal generate spesifikasi OpenAPI. Pastikan anotasi #[OA\Info] sudah ada.']));
        }

        return $this->response
            ->setStatusCode(200)
            ->setContentType('application/json')
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setBody($openapi->toJson());
    }
}

// ========================
// CONTOH ANOTASI ENDPOINT
// ========================

/**
 * Kelas ini menyimpan anotasi endpoint sebagai contoh dokumentasi API.
 * Letakkan anotasi endpoint aktual di dalam Controller masing-masing.
 */
class AuthApiDocs
{
    #[OA\Post(
        path: '/login/process',
        operationId: 'loginProcess',
        summary: 'Proses login pengguna',
        description: 'Memvalidasi username & password, lalu membuat session login.',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    required: ['username', 'password'],
                    properties: [
                        new OA\Property(property: 'username', type: 'string', example: 'john.doe'),
                        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'rahasia123'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 302, description: 'Login berhasil, redirect ke /dashboard'),
            new OA\Response(response: 400, description: 'Input tidak valid atau password salah'),
        ]
    )]
    public function loginProcess(): void {}

    #[OA\Get(
        path: '/logout',
        operationId: 'logout',
        summary: 'Logout pengguna',
        description: 'Menghapus session aktif dan redirect ke halaman login.',
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 302, description: 'Redirect ke /login setelah logout berhasil'),
        ]
    )]
    public function logout(): void {}
}

class DashboardApiDocs
{
    #[OA\Get(
        path: '/dashboard',
        operationId: 'getDashboard',
        summary: 'Halaman dashboard pengguna',
        description: 'Menampilkan dashboard. Hanya dapat diakses oleh pengguna yang sudah login (protected by AuthFilter).',
        tags: ['Dashboard'],
        security: [['sessionAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Halaman dashboard berhasil ditampilkan'),
            new OA\Response(response: 302, description: 'Redirect ke /login jika belum terautentikasi'),
        ]
    )]
    public function getDashboard(): void {}
}

class UserApiDocs
{
    #[OA\Get(
        path: '/users',
        operationId: 'getUsers',
        summary: 'Daftar Pengguna',
        description: 'Mendapatkan daftar seluruh pengguna SIAMA (Akses dibatasi sesuai Role).',
        tags: ['Users'],
        security: [['sessionAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Menampilkan halaman tabel pengguna'),
        ]
    )]
    public function getUsers(): void {}

    #[OA\Post(
        path: '/users/store',
        operationId: 'storeUser',
        summary: 'Tambah Pengguna Baru',
        tags: ['Users'],
        security: [['sessionAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    required: ['nama', 'email', 'id_role'],
                    properties: [
                        new OA\Property(property: 'nama', type: 'string'),
                        new OA\Property(property: 'email', type: 'string'),
                        new OA\Property(property: 'id_role', type: 'integer'),
                        new OA\Property(property: 'id_opd', type: 'string'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 302, description: 'Redirect setelah sukses menyimpan'),
        ]
    )]
    public function storeUser(): void {}
}

class ArsipApiDocs
{
    #[OA\Get(
        path: '/arsip',
        operationId: 'getArsip',
        summary: 'Daftar Arsip',
        tags: ['Arsip'],
        security: [['sessionAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Halaman daftar arsip'),
        ]
    )]
    public function getArsip(): void {}
    
    #[OA\Post(
        path: '/arsip/store',
        operationId: 'storeArsip',
        summary: 'Upload Arsip Baru',
        tags: ['Arsip'],
        security: [['sessionAuth' => []]],
        responses: [
            new OA\Response(response: 302, description: 'Redirect setelah sukses upload'),
        ]
    )]
    public function storeArsip(): void {}
}

class BeritaAcaraApiDocs
{
    #[OA\Get(
        path: '/berita-acara',
        operationId: 'getBA',
        summary: 'Daftar Berita Acara',
        tags: ['Berita Acara'],
        security: [['sessionAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Halaman Berita Acara'),
        ]
    )]
    public function getBA(): void {}
}

class PengawasanOpdApiDocs
{
    #[OA\Get(
        path: '/pengawasan',
        operationId: 'getPengawasan',
        summary: 'Dashboard Pengawasan OPD',
        tags: ['Pengawasan OPD'],
        security: [['sessionAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Menampilkan persentase capaian OPD'),
        ]
    )]
    public function getPengawasan(): void {}
}
