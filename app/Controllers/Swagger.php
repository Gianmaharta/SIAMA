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
