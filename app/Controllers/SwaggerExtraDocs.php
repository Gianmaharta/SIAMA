<?php

namespace App\Controllers;

use OpenApi\Attributes as OA;

/**
 * File ini berisi contoh dokumentasi OpenAPI/Swagger untuk seluruh sisa endpoint SIAMA.
 * Ini melengkapi dokumentasi API agar seluruh sistem tercakup dalam Swagger UI.
 */

class SwaggerExtraDocs
{
    #[OA\Get(path: '/opd', summary: 'Daftar Master OPD', tags: ['Master OPD'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman daftar OPD')])]
    public function opdIndex(): void {}

    #[OA\Post(path: '/opd/store', summary: 'Simpan OPD Baru', tags: ['Master OPD'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function opdStore(): void {}

    #[OA\Get(path: '/bidang', summary: 'Daftar Master Bidang', tags: ['Master Bidang'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman daftar Bidang')])]
    public function bidangIndex(): void {}

    #[OA\Post(path: '/bidang/store', summary: 'Simpan Bidang Baru', tags: ['Master Bidang'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function bidangStore(): void {}

    #[OA\Get(path: '/kode-klasifikasi', summary: 'Daftar Kode Klasifikasi', tags: ['Kode Klasifikasi'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman daftar kode klasifikasi')])]
    public function kodeIndex(): void {}

    #[OA\Post(path: '/kode-klasifikasi/store', summary: 'Simpan Kode Klasifikasi', tags: ['Kode Klasifikasi'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function kodeStore(): void {}

    #[OA\Get(path: '/jra', summary: 'Daftar Jadwal Retensi Arsip (JRA)', tags: ['Master JRA'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman daftar JRA')])]
    public function jraIndex(): void {}

    #[OA\Post(path: '/jra/store', summary: 'Simpan JRA Baru', tags: ['Master JRA'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function jraStore(): void {}

    #[OA\Get(path: '/jra/get_by_klasifikasi/{id}', summary: 'Ambil JRA berdasarkan Klasifikasi (AJAX JSON)', tags: ['Master JRA'], security: [['sessionAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 200, description: 'Data JSON JRA')]
    )]
    public function get_by_klasifikasi(): void {}

    #[OA\Get(path: '/notification', summary: 'Daftar Notifikasi', tags: ['Notifikasi'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman daftar notifikasi')])]
    public function notifIndex(): void {}

    #[OA\Post(path: '/notification/action/{id}', summary: 'Aksi Notifikasi (Tandai Dibaca/Musnahkan)', tags: ['Notifikasi'], security: [['sessionAuth' => []]], 
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')]
    )]
    public function notifAction(): void {}

    #[OA\Get(path: '/penilaian', summary: 'Daftar Penilaian Arsip', tags: ['Penilaian Arsip'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman daftar penilaian')])]
    public function penIndex(): void {}

    #[OA\Get(path: '/penilaian/form/{id}', summary: 'Formulir Penilaian Arsip', tags: ['Penilaian Arsip'], security: [['sessionAuth' => []]], 
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 200, description: 'Halaman form penilaian')]
    )]
    public function penForm(): void {}

    #[OA\Post(path: '/penilaian/store/{id}', summary: 'Simpan Penilaian Arsip', tags: ['Penilaian Arsip'], security: [['sessionAuth' => []]], 
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses menyimpan')]
    )]
    public function penStore(): void {}

    #[OA\Get(path: '/spt', summary: 'Daftar Surat Perintah Tugas (SPT)', tags: ['Surat Perintah Tugas (SPT)'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman daftar SPT')])]
    public function sptIndex(): void {}

    #[OA\Post(path: '/spt/store', summary: 'Upload SPT Baru', tags: ['Surat Perintah Tugas (SPT)'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 302, description: 'Redirect setelah sukses')])]
    public function sptStore(): void {}

    #[OA\Post(path: '/spt/process-assign/{id}', summary: 'Penugasan Arsiparis pada SPT', tags: ['Surat Perintah Tugas (SPT)'], security: [['sessionAuth' => []]], 
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 302, description: 'Redirect setelah menugaskan')]
    )]
    public function processAssign(): void {}

    #[OA\Get(path: '/audit-log/access', summary: 'Log Akses (Login/Logout)', tags: ['Audit Log'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman Access Log')])]
    public function accessLog(): void {}

    #[OA\Get(path: '/audit-log/activity', summary: 'Log Aktivitas Sistem (CRUD)', tags: ['Audit Log'], security: [['sessionAuth' => []]], responses: [new OA\Response(response: 200, description: 'Halaman Activity Log')])]
    public function activityLog(): void {}
}
