<?php

namespace Tests\App\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class BeritaAcaraDetailPageTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $DBGroup     = 'tests';
    protected $migrate     = false;
    protected $migrateOnce = false;
    protected $refresh     = false;
    protected $seed        = '';

    protected function sessAdminPemkab(): array
    {
        return [
            'user_id'             => 'admin-pemkab-test-id',
            'email'               => 'admin@pemkab.test',
            'nama_lengkap'        => 'Administrator Pemkab',
            'nama_role'           => 'Admin_Pemkab',
            'id_role'             => '1',
            'id_opd'              => null,
            'id_bidang'           => null,
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];
    }

    public function testViewBeritaAcaraDetailPageRendersAccurately(): void
    {
        $db = \Config\Database::connect();
        
        // Buat atau ambil BA dari DB
        $existingBa = $db->table('berita_acara')->get(1)->getRowArray();
        $idBa = $existingBa['id_berita_acara'] ?? null;

        if (!$idBa) {
            $idBa = 'ba-test-' . time();
            $db->table('berita_acara')->insert([
                'id_berita_acara'    => $idBa,
                'nomor_ba'           => '018/BA/BPKPD/2026',
                'tanggal_ba'         => '2026-09-05',
                'status_persetujuan' => 'Draf_Kabid',
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ]);
        }

        $result = $this->withSession($this->sessAdminPemkab())
                       ->get('/berita-acara/detail/' . $idBa);

        $result->assertOK();
        $body = $result->getBody();

        // 1. Header
        $result->assertSee('Detail Berita Acara');
        $this->assertStringContainsString('ba-btn-back-header', $body);
        $this->assertStringNotContainsString('Kembali</', $body);

        // 2. Section 1: Informasi Metadata
        $result->assertSee('Informasi Metadata');
        $result->assertSee('Nomor BA');
        $result->assertSee('Tanggal');
        $result->assertSee('Nomor SPT');
        $result->assertSee('OPD');
        $result->assertSee('Status');
        $this->assertStringContainsString('ba-status-badge', $body);

        // 3. Section 2: Daftar Arsip
        $result->assertSee('Daftar Arsip');
        $result->assertSee('Nomor Arsip');
        $result->assertSee('Nama Arsip');
        $result->assertSee('Kurun Waktu');
        $result->assertSee('Kondisi');
        $this->assertStringContainsString('ba-arsip-link', $body);

        // 4. Section 3: Riwayat Penandatanganan Horizontal
        $result->assertSee('Riwayat Penandatanganan');
        $result->assertSee('Pelaksana/Arsiparis');
        $result->assertSee('Kepala Bidang');
        $result->assertSee('Pimpinan');
        $result->assertSee('Status: Selesai');
        $this->assertStringContainsString('bi-check-circle-fill', $body);
        $this->assertStringContainsString('ba-sig-track', $body);

        // 5. Sidebar Check: Menu Berita Acara aktif, no unneeded elements
        $this->assertStringContainsString('Berita Acara', $body);
    }

    protected function sessArsiparis(): array
    {
        return [
            'user_id'             => '08167f77-3106-4010-b801-b0716a2809d8',
            'email'               => 'arsiparis@siama.test',
            'nama_lengkap'        => 'Budi Santoso',
            'nama_role'           => 'Arsiparis',
            'id_role'             => '5',
            'id_opd'              => '9974e50e-9512-4044-999d-7eb4061cd6b2',
            'id_bidang'           => 'fb5bbc04-7b27-43a6-8010-9b59d64e0cca',
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];
    }

    public function testArsiparisCanAccessCreateBeritaAcaraPage(): void
    {
        $result = $this->withSession($this->sessArsiparis())
                       ->get('/berita-acara/create');

        $result->assertOK();
        $body = $result->getBody();

        $result->assertSee('Buat Berita Acara');
        $result->assertSee('Informasi Berita Acara');
        $result->assertSee('Nomor Berita Acara');
        $result->assertSee('Tanggal Berita Acara');
        $result->assertSee('Pilih Arsip yang Dialihmediakan');
        $result->assertSee('Simpan Draf Berita Acara');
        $result->assertSee('Batal');
        $this->assertStringContainsString('ba-btn-back-header', $body);
    }

    public function testArsiparisCanStoreBeritaAcara(): void
    {
        $db = \Config\Database::connect();
        
        // Buat arsip test yang belum punya id_berita_acara
        $idArsip = 'arsip-test-ba-' . time();
        $db->table('arsip')->insert([
            'id_arsip'            => $idArsip,
            'id_opd'              => '9974e50e-9512-4044-999d-7eb4061cd6b2',
            'id_bidang'           => 'fb5bbc04-7b27-43a6-8010-9b59d64e0cca',
            'nomor_arsip'         => 'TEST/BA/' . time(),
            'nama_arsip'          => 'Dokumen Uji BA ' . time(),
            'kurun_waktu'         => '2026',
            'kondisi'             => 'Baik',
            'status_verifikasi'   => 'Selesai',
            'id_berita_acara'     => null,
            'created_at'          => date('Y-m-d H:i:s'),
        ]);

        $nomorBa = '099/BA/TEST/' . time();
        $postData = [
            'nomor_ba'   => $nomorBa,
            'tanggal_ba' => date('Y-m-d'),
            'arsip_ids'  => [$idArsip],
        ];

        $result = $this->withSession($this->sessArsiparis())
                       ->post('/berita-acara/store', $postData);

        $result->assertRedirectTo('/berita-acara');

        // Verifikasi di database
        $createdBa = $db->table('berita_acara')->where('nomor_ba', $nomorBa)->get()->getRowArray();
        $this->assertNotNull($createdBa);
        $this->assertEquals('Draf_Kabid', $createdBa['status_persetujuan']);

        // Verifikasi relasi di berita_acara_detail dan arsip
        $detail = $db->table('berita_acara_detail')->where('id_berita_acara', $createdBa['id_berita_acara'])->get()->getRowArray();
        $this->assertNotNull($detail);
        $this->assertEquals($idArsip, $detail['id_arsip']);

        $arsip = $db->table('arsip')->where('id_arsip', $idArsip)->get()->getRowArray();
        $this->assertEquals($createdBa['id_berita_acara'], $arsip['id_berita_acara']);
    }

    public function testArsiparisCanAccessEditBeritaAcaraPage(): void
    {
        $db = \Config\Database::connect();
        $idBa = 'ba-edit-test-' . time();

        $db->table('berita_acara')->insert([
            'id_berita_acara'    => $idBa,
            'id_opd'             => '9974e50e-9512-4044-999d-7eb4061cd6b2',
            'nomor_ba'           => 'EDIT/BA/' . time(),
            'tanggal_ba'         => '2026-09-10',
            'status_persetujuan' => 'Draf_Kabid',
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);

        $result = $this->withSession($this->sessArsiparis())
                       ->get('/berita-acara/edit/' . $idBa);

        $result->assertOK();
        $body = $result->getBody();

        $result->assertSee('Edit Draf Berita Acara');
        $result->assertSee('Informasi Berita Acara');
        $result->assertSee('Nomor Berita Acara');
        $result->assertSee('Tanggal Berita Acara');
        $result->assertSee('Kelola Arsip Terlampir');
        $result->assertSee('Simpan Perubahan');
        $result->assertSee('Batal');
        $this->assertStringContainsString('ba-btn-back-header', $body);
    }

    public function testArsiparisCanUpdateBeritaAcara(): void
    {
        $db = \Config\Database::connect();
        $idBa = 'ba-upd-test-' . time();
        $idArsip1 = 'arsip-u1-' . time();
        $idArsip2 = 'arsip-u2-' . time();

        // Buat 2 arsip
        $db->table('arsip')->insert([
            'id_arsip'          => $idArsip1,
            'id_opd'            => '9974e50e-9512-4044-999d-7eb4061cd6b2',
            'nomor_arsip'       => 'ARS/1/' . time(),
            'nama_arsip'        => 'Arsip Awal ' . time(),
            'status_verifikasi' => 'Selesai',
            'id_berita_acara'   => $idBa,
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        $db->table('arsip')->insert([
            'id_arsip'          => $idArsip2,
            'id_opd'            => '9974e50e-9512-4044-999d-7eb4061cd6b2',
            'nomor_arsip'       => 'ARS/2/' . time(),
            'nama_arsip'        => 'Arsip Pengganti ' . time(),
            'status_verifikasi' => 'Selesai',
            'id_berita_acara'   => null,
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        // Buat BA dengan status Revisi
        $db->table('berita_acara')->insert([
            'id_berita_acara'    => $idBa,
            'id_opd'             => '9974e50e-9512-4044-999d-7eb4061cd6b2',
            'nomor_ba'           => 'OLD/BA/' . time(),
            'tanggal_ba'         => '2026-09-01',
            'status_persetujuan' => 'Revisi',
            'catatan_revisi'     => 'Mohon perbaiki tanggal dan arsip terkait',
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);

        $db->table('berita_acara_detail')->insert([
            'id_berita_acara' => $idBa,
            'id_arsip'        => $idArsip1,
        ]);

        $newNomorBa = 'NEW/BA/' . time();
        $postData = [
            'nomor_ba'   => $newNomorBa,
            'tanggal_ba' => '2026-09-15',
            'arsip_ids'  => [$idArsip2], // Tukar arsip 1 dengan arsip 2
        ];

        $result = $this->withSession($this->sessArsiparis())
                       ->post('/berita-acara/update/' . $idBa, $postData);

        $result->assertRedirectTo('/berita-acara');

        // Verifikasi update
        $updatedBa = $db->table('berita_acara')->where('id_berita_acara', $idBa)->get()->getRowArray();
        $this->assertEquals($newNomorBa, $updatedBa['nomor_ba']);
        $this->assertEquals('2026-09-15', $updatedBa['tanggal_ba']);
        // Status Revisi kembali menjadi Draf_Kabid untuk ditinjau ulang
        $this->assertEquals('Draf_Kabid', $updatedBa['status_persetujuan']);

        // Verifikasi arsip 1 dilepas
        $arsip1 = $db->table('arsip')->where('id_arsip', $idArsip1)->get()->getRowArray();
        $this->assertNull($arsip1['id_berita_acara']);

        // Verifikasi arsip 2 terpasang
        $arsip2 = $db->table('arsip')->where('id_arsip', $idArsip2)->get()->getRowArray();
        $this->assertEquals($idBa, $arsip2['id_berita_acara']);
    }
}
