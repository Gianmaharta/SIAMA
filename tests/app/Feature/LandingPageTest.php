<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class LandingPageTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testLandingPageRendersSuccessfully()
    {
        $result = $this->get('/');
        $result->assertOK();

        // 1. Header & Brand
        $result->assertSee('SIAMA Buleleng');
        $result->assertSee('Dinas Kearsipan &amp; Perpustakaan Daerah');

        // 2. Hero Section (clean & classy dengan heading, deskripsi, dan tombol aksi frosted glass)
        $result->assertSee('Layanan Alih Media Arsip');
        $result->assertSee('Digital');
        $result->assertSee('Panduan &amp; Regulasi');
        $this->assertStringNotContainsString('heroSearchWidget', $result->getBody());
        $this->assertStringNotContainsString('lp-hero-glass-panel', $result->getBody());

        // 3. Workflow 5 Tahapan
        $result->assertSee('5 Tahapan Alih Media Arsip di SIAMA');
        $result->assertSee('Penerbitan SPT');
        $result->assertSee('Penugasan Arsiparis');
        $result->assertSee('Unggah &amp; Watermark');
        $result->assertSee('Verifikasi Kabid');
        $result->assertSee('Pengesahan &amp; Evaluasi');

        // 4. Peran & Otoritas
        $result->assertSee('Siapa Saja yang Menggunakan Aplikasi SIAMA?');
        $result->assertSee('Pimpinan OPD');
        $result->assertSee('Kepala Bidang');
        $result->assertSee('Arsiparis Pelaksana');
        $result->assertSee('Admin OPD / Admin Pemkab');

        // 5. Standar Tata Kelola
        $result->assertSee('Tata Kelola Alih Media Arsip Terstandar &amp; Teruji');
        $result->assertSee('Penugasan SPT');
        $result->assertSee('Jadwal Retensi (JRA)');
        $result->assertSee('TTE &amp; Watermark');
        $result->assertSee('Berita Acara Otentik');

        // 6. Section Cari Arsip
        $result->assertSee('Cari Arsip');
        $result->assertSee('Temukan informasi arsip berdasarkan kata kunci');

        // 7. Bantuan & Footer
        $result->assertSee('Pusat Asistensi &amp; Layanan Bantuan SIAMA');
        $result->assertSee('ANRI');
        $result->assertSee('BSrE BSSN');
        $result->assertSee('Diskominfosanti');
    }

    public function testSearchValidationForEmptyAndShortKeyword()
    {
        // 1. Empty query
        $resEmpty = $this->get('/cari-arsip?q=');
        $resEmpty->assertOK();
        $jsonEmpty = json_decode($resEmpty->getJSON(), true);
        $this->assertEquals('error', $jsonEmpty['status']);
        $this->assertStringContainsString('Silakan masukkan', $jsonEmpty['message']);

        // 2. 1 character query
        $resShort = $this->get('/cari-arsip?q=a');
        $resShort->assertOK();
        $jsonShort = json_decode($resShort->getJSON(), true);
        $this->assertEquals('error', $jsonShort['status']);
        $this->assertStringContainsString('minimal 2 karakter', $jsonShort['message']);
    }

    public function testSearchEndpointReturnsDataSafely()
    {
        $res = $this->get('/cari-arsip?q=ARSIP');
        $res->assertOK();
        $json = json_decode($res->getJSON(), true);

        $this->assertEquals('success', $json['status']);
        $this->assertArrayHasKey('count', $json);
        $this->assertArrayHasKey('data', $json);

        if (!empty($json['data'])) {
            $item = $json['data'][0];
            $this->assertArrayHasKey('nomor_arsip', $item);
            $this->assertArrayHasKey('nama_arsip', $item);
            $this->assertArrayHasKey('nama_opd', $item);
            $this->assertArrayHasKey('akses_publik', $item);
            $this->assertArrayHasKey('is_terbuka', $item);
            // Ensure sensitive fields like physical file paths are NOT exposed to public
            $this->assertArrayNotHasKey('file_arsip', $item);
            $this->assertArrayNotHasKey('file_path', $item);
            $this->assertArrayNotHasKey('password', $item);
        }
    }
}
