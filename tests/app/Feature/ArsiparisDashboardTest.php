<?php

namespace Tests\App\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class ArsiparisDashboardTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $DBGroup     = 'tests';
    protected $migrate     = false;
    protected $migrateOnce = false;
    protected $refresh     = false;
    protected $seed        = '';

    protected function sessArsiparis(string $idUser = '08167f77-3106-4010-b801-b0716a2809d8', string $nama = 'Budi Santoso'): array
    {
        return [
            'user_id'             => $idUser,
            'email'               => 'arsiparis@siama.test',
            'nama_lengkap'        => $nama,
            'nama_role'           => 'Arsiparis',
            'id_role'             => '5',
            'id_opd'              => '9974e50e-9512-4044-999d-7eb4061cd6b2',
            'id_bidang'           => 'fb5bbc04-7b27-43a6-8010-9b59d64e0cca',
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];
    }

    public function testArsiparisDashboardRendersAccurately()
    {
        $result = $this->withSession($this->sessArsiparis('08167f77-3106-4010-b801-b0716a2809d8', 'Budi Santoso'))
                       ->get('/dashboard');

        $result->assertOK();
        $body = $result->getBody();

        // 1. Heading & Subheading
        $result->assertSee('Selamat Datang, Budi Santoso!');
        $result->assertSee('Ini adalah ringkasan aktivitas Anda sebagai Arsiparis.');

        // 2. Summary Cards
        $result->assertSee('Total Arsip yang Anda Unggah');
        $result->assertSee('Penugasan SPT Anda');

        // 3. Menu Akses Cepat (4 Cards)
        $result->assertSee('Menu Akses Cepat');
        $result->assertSee('Pilih menu untuk mengelola data sesuai dengan tugas Anda sebagai Arsiparis.');
        $result->assertSee('Pengelolaan Arsip');
        $result->assertSee('Kelola dan unggah arsip digital');
        $result->assertSee('Surat Perintah Tugas (SPT)');
        $result->assertSee('Lihat daftar penugasan SPT Anda');
        $result->assertSee('Berita Acara');
        $result->assertSee('Lihat dan kelola berita acara');
        $result->assertSee('Notifikasi');
        $result->assertSee('Lihat pemberitahuan sistem');

        // 4. Status Verifikasi Arsip (Donut Chart)
        $result->assertSee('Status Verifikasi Arsip');
        $result->assertSee('Rasio kelayakan berkas yang telah Anda unggah');
        $result->assertSee('arsiparisDonutChart');
        $result->assertSee('Total Arsip');
        $result->assertSee('Selesai Diverifikasi');
        $result->assertSee('Dalam Proses Telaah');
        $result->assertSee('Ditolak / Revisi');
        $this->assertStringNotContainsString('Realtime', $body);
        $this->assertStringNotContainsString('status-item-pct', $body);

        // 5. Tren Pengunggahan Arsip (Bar Chart)
        $result->assertSee('Tren Pengunggahan Arsip');
        $result->assertSee('Volume alih media dokumen arsip per bulan');
        $result->assertSee('arsiparisBarChart');
        $result->assertSee('Rata-rata produktivitas:');
        $result->assertSee('Lihat Laporan Detail');

        // 6. Berita Acara Menanti Verifikasi Table
        $result->assertSee('Berita Acara Menanti Verifikasi');

        // 7. Sidebar 4 Clean Menus
        $result->assertSee('Surat Perintah Tugas (SPT)');
        $result->assertSee('Pengelolaan Arsip');
        $result->assertSee('Berita Acara');

        // 8. Admin menus must NOT be present for Arsiparis
        $this->assertStringNotContainsString('Manajemen Akun', $body);
        $this->assertStringNotContainsString('Master OPD', $body);
        $this->assertStringNotContainsString('Master Bidang', $body);
        $this->assertStringNotContainsString('Penilaian Arsip', $body);
        $this->assertStringNotContainsString('Master JRA', $body);
        $this->assertStringNotContainsString('Access Log', $body);
        $this->assertStringNotContainsString('Activity Log', $body);
    }

    public function testArsiparisDashboardWithNoDataShowsCleanEmptyState()
    {
        // Pengguna baru tanpa arsip / berita acara
        $sessionData = $this->sessArsiparis('user-arsiparis-empty', 'Ni Luh Ayu');

        $result = $this->withSession($sessionData)->get('/dashboard');
        $result->assertOK();

        $result->assertSee('Selamat Datang, Ni Luh Ayu!');
        $result->assertSee('Total: 0 Berkas');
        $result->assertSee('Tidak ada data');
        $result->assertSee('Belum ada berita acara yang perlu diverifikasi.');
    }
}
