<?php

namespace App\Controllers;

use OpenApi\Attributes as OA;

/**
 * File ini adalah tempat deklarasi sentral anotasi OpenAPI/Swagger untuk seluruh SIAM.
 * Tidak berisi logic, hanya digunakan sebagai sumber informasi untuk swagger-php scanner.
 */
#[OA\Info(
    version: '1.0.0',
    title: 'API Dokumentasi SIAMA',
    description: 'Dokumentasi API untuk Sistem Informasi Alih Media Arsip (SIAMA) berbasis CodeIgniter 4. Mengelola alih media dokumen fisik ke digital, SPT, arsip, dan Berita Acara.',
    contact: new OA\Contact(
        name: 'Tim SIAMA',
        email: 'admin@siama.test'
    )
)]
#[OA\Server(
    url: 'http://siama.test',
    description: 'Server Development Lokal'
)]
#[OA\SecurityScheme(
    securityScheme: 'sessionAuth',
    type: 'apiKey',
    in: 'cookie',
    name: 'ci_session',
    description: 'Autentikasi berbasis session CodeIgniter 4'
)]

// --- TAG GROUPING ---
#[OA\Tag(name: 'Auth', description: 'Endpoint untuk autentikasi pengguna')]
#[OA\Tag(name: 'Dashboard', description: 'Endpoint untuk dashboard pengguna')]

class OpenApiSpec
{
    // Kelas ini sengaja dikosongkan.
    // Hanya sebagai "holder" untuk anotasi global OpenAPI.
}
