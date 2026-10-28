<?php

namespace Tests\App\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class KabidDashboardTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $DBGroup     = 'tests';
    protected $migrate     = false;
    protected $migrateOnce = false;
    protected $refresh     = false;
    protected $seed        = '';

    protected function sessKabid(string $nama = 'Budi Santoso'): array
    {
        return [
            'user_id'             => 'user-kabid-001',
            'email'               => 'kabid@siama.test',
            'nama_lengkap'        => $nama,
            'nama_role'           => 'Kepala_Bidang',
            'id_role'             => '4',
            'id_opd'              => 'opd-test-1',
            'id_bidang'           => 'bidang-test-1',
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];
    }

    public function testKabidDashboardRendersAccurately()
    {
        $result = $this->withSession($this->sessKabid('Budi Santoso'))
                       ->get('/dashboard');

        $result->assertOK();
        $body = $result->getBody();

        // 1. Heading & Subheading
        $result->assertSee('Selamat Datang, Budi Santoso!');
        $result->assertSee('Ini adalah ringkasan statistik berdasarkan akses Anda sebagai Kepala Bidang');

        // 2. Summary Cards
        $result->assertSee('Total Arsip di Bidang Anda');
        $result->assertSee('Berita Acara Menanti Verifikasi');

        // 3. Content Section (Charts)
        $result->assertSee('Status Arsip di Bidang Anda');
        $result->assertSee('kabidDonutChart');
        $result->assertSee('Arsip per Bulan (2026)');
        $result->assertSee('kabidBarChart');

        // 4. Donut Legend items
        $result->assertSee('Selesai');
        $result->assertSee('Dalam Proses');
        $result->assertSee('Belum Diproses');
        $result->assertSee('Gagal / Ditolak');

        // 5. Berita Acara Section
        $result->assertSee('Berita Acara Menanti Verifikasi Kabid');
        $result->assertSee('Tidak ada data');
        $result->assertSee('Saat ini belum ada berita acara yang menunggu verifikasi Anda.');

        // 6. Sidebar 4 Clean Menus
        $result->assertSee('Surat Perintah Tugas (SPT)');
        $result->assertSee('Pengelolaan Arsip');
        $result->assertSee('Berita Acara');

        // 7. Admin menus must NOT be present
        $this->assertStringNotContainsString('Manajemen Akun', $body);
        $this->assertStringNotContainsString('Master OPD', $body);
        $this->assertStringNotContainsString('Master JRA', $body);
        $this->assertStringNotContainsString('Access Log', $body);
        $this->assertStringNotContainsString('Activity Log', $body);
    }

    public function testKabidDashboardLoadsWithRealOpdAndBidang()
    {
        $sessionData = [
            'user_id'             => 'ec666f93-376d-4616-9d3e-62317849764e',
            'email'               => 'kabid@siama.test',
            'nama_lengkap'        => 'Kepala Bidang Kominfo',
            'nama_role'           => 'Kepala_Bidang',
            'id_role'             => '42d1c643-9b91-4997-851d-0c1309897b4e',
            'id_opd'              => '9974e50e-9512-4044-999d-7eb4061cd6b2',
            'id_bidang'           => 'fb5bbc04-7b27-43a6-8010-9b59d64e0cca',
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];

        $result = $this->withSession($sessionData)->get('/dashboard');
        $result->assertOK();

        $result->assertSee('Selamat Datang, Kepala Bidang Kominfo!');
        $result->assertSee('Status Arsip di Bidang Anda');
        $result->assertSee('Arsip per Bulan (2026)');
    }

    public function testKabidChartDataEndpointReturnsJson()
    {
        $sessionData = [
            'user_id'             => 'ec666f93-376d-4616-9d3e-62317849764e',
            'email'               => 'kabid@siama.test',
            'nama_lengkap'        => 'Kepala Bidang Kominfo',
            'nama_role'           => 'Kepala_Bidang',
            'id_role'             => '42d1c643-9b91-4997-851d-0c1309897b4e',
            'id_opd'              => '9974e50e-9512-4044-999d-7eb4061cd6b2',
            'id_bidang'           => 'fb5bbc04-7b27-43a6-8010-9b59d64e0cca',
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];

        $result = $this->withSession($sessionData)->get('/dashboard/kabid-chart-data?year=2026');
        $result->assertOK();

        $json = json_decode($result->getJSON(), true);
        $this->assertIsArray($json);
        $this->assertSame('success', $json['status'] ?? null);
        $this->assertSame('2026', $json['year'] ?? null);
        $this->assertCount(12, $json['values'] ?? []);
        $this->assertCount(12, $json['labels'] ?? []);
    }
}
