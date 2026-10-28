<?php

namespace Tests\App\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class ArsipPageTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $DBGroup     = 'tests';
    protected $migrate     = false;
    protected $migrateOnce = false;
    protected $refresh     = false;
    protected $seed        = '';

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

    public function testArsiparisAccessesPengelolaanArsipPage()
    {
        $result = $this->withSession($this->sessArsiparis())
                       ->get('/arsip');

        $result->assertOK();
        $body = $result->getBody();

        // 1. Header & Subtitle
        $result->assertSee('Pengelolaan Arsip');
        $result->assertSee('Kelola dan unggah arsip digital sesuai ketentuan dan jadwal retensi arsip (JRA).');

        // 2. Toolbar & CTA Button
        $result->assertSee('Cari nomor, nama arsip, atau bidang...');
        $result->assertSee('Tambah Arsip');
        $result->assertSee('filterBidang');
        $result->assertSee('filterStatus');

        // 3. Table Column Headers
        $result->assertSee('NOMOR ARSIP');
        $result->assertSee('NAMA ARSIP');
        $result->assertSee('KODE KLASIFIKASI');
        $result->assertSee('BIDANG');
        $result->assertSee('SKOR PRIORITAS');
        $result->assertSee('STATUS VERIFIKASI');
        $result->assertSee('BERKAS FILE');
        $result->assertSee('AKSI');

        // 4. Empty State Elements
        $result->assertSee('Belum ada arsip');
        $result->assertSee('Belum ada arsip yang tersedia untuk dikelola.');

        // 5. Sidebar check for Arsiparis (No admin menus)
        $this->assertStringNotContainsString('Manajemen Akun', $body);
        $this->assertStringNotContainsString('Master OPD', $body);
        $this->assertStringNotContainsString('Master Bidang', $body);
        $this->assertStringNotContainsString('Penilaian Arsip', $body);
        $this->assertStringNotContainsString('Master JRA', $body);
        $this->assertStringNotContainsString('Access Log', $body);
        $this->assertStringNotContainsString('Activity Log', $body);
    }

    public function testArsiparisAccessesEditArsipPage()
    {
        $db = \Config\Database::connect();
        $arsip = $db->table('arsip')->get(1)->getRowArray();
        $idArsip = $arsip['id_arsip'] ?? '97f2537d-9aad-4ee1-a86a-7099274c8ed1';

        $result = $this->withSession($this->sessArsiparis())
                       ->get('/arsip/edit/' . $idArsip);

        $result->assertOK();
        $body = $result->getBody();

        // 1. Header
        $result->assertSee('Edit Arsip');

        // 2. Tiga Card Utama
        $result->assertSee('Informasi Utama');
        $result->assertSee('Informasi Jadwal Retensi Arsip (JRA)');
        $result->assertSee('Berkas Digital');

        // 3. Form Field Labels
        $result->assertSee('Nomor Arsip');
        $result->assertSee('Nama / Judul Arsip');
        $result->assertSee('Kode Klasifikasi');
        $result->assertSee('Bidang');
        $result->assertSee('SPT Terkait');
        $result->assertSee('Tahun Penciptaan');
        $result->assertSee('Kondisi Fisik');
        $result->assertSee('Status Verifikasi');

        // 4. JRA Fields
        $result->assertSee('Retensi Aktif (Tahun)');
        $result->assertSee('Retensi Inaktif (Tahun)');
        $result->assertSee('Keterangan Retensi');
        $result->assertSee('Tingkat Keamanan');
        $result->assertSee('Dasar Pertimbangan');

        // 5. Digital File & Watermark
        $result->assertSee('Ganti Berkas Digital');
        $result->assertSee('Opsi Keamanan PDF (Watermark)');
        $result->assertSee('Generate Watermark Sistem (Khusus PDF)');

        // 6. Action Buttons
        $result->assertSee('Simpan Perubahan');
        $result->assertSee('Batal');

        // 7. Sidebar check (No admin menus)
        $this->assertStringNotContainsString('Manajemen Akun', $body);
        $this->assertStringNotContainsString('Master OPD', $body);
        $this->assertStringNotContainsString('Master Bidang', $body);
        $this->assertStringNotContainsString('Penilaian Arsip', $body);
    }

    public function testArsiparisCanUpdateArsip()
    {
        $db = \Config\Database::connect();
        $arsip = $db->table('arsip')->get(1)->getRowArray();
        $idArsip = $arsip['id_arsip'] ?? '97f2537d-9aad-4ee1-a86a-7099274c8ed1';

        $updatedNama = 'Laporan Kegiatan Update ' . time();
        $postData = [
            'nomor_arsip'         => $arsip['nomor_arsip'] ?? '001/ARSIP/DISPERTAN/2026',
            'nama_arsip'          => $updatedNama,
            'id_kode_klasifikasi' => $arsip['id_kode_klasifikasi'] ?? '97f2537d-9aad-4ee1-a86a-7099274c8ed1',
            'id_bidang'           => $arsip['id_bidang'] ?? 'e372229d-552b-4f99-b006-6334f4bd8d3e',
            'id_spt'              => $arsip['id_spt'] ?? null,
            'kurun_waktu'         => '2026',
            'kondisi'             => 'Baik',
            'status_verifikasi'   => $arsip['status_verifikasi'] ?? 'Draft',
        ];

        $result = $this->withSession($this->sessArsiparis())
                       ->post('/arsip/update/' . $idArsip, $postData);

        $result->assertRedirectTo('/arsip');

        // Verifikasi di database
        $check = $db->table('arsip')->where('id_arsip', $idArsip)->get()->getRowArray();
        $this->assertEquals($updatedNama, $check['nama_arsip']);
    }

    public function testArsiparisAccessesCreateArsipPage()
    {
        $result = $this->withSession($this->sessArsiparis())
                       ->get('/arsip/create');

        $result->assertOK();
        $body = $result->getBody();

        // 1. Header & Subtitle
        $result->assertSee('Tambah Arsip');
        $result->assertSee('Tambahkan data arsip digital baru ke dalam sistem.');

        // 2. Tiga Card Utama
        $result->assertSee('Informasi Arsip');
        $result->assertSee('Informasi Jadwal Retensi Arsip (JRA)');
        
        // Pastikan Berkas Digital HANYA SATU KALI (tidak terduplikasi)
        $this->assertEquals(1, substr_count($body, '<span>Berkas Digital</span>'));
        $this->assertStringNotContainsString('Ganti Berkas Digital', $body);
        $this->assertStringNotContainsString('Simpan Perubahan', $body);

        // 3. Form Field Labels pada Informasi Arsip
        $result->assertSee('Nomor Arsip');
        $result->assertSee('Nama / Judul Arsip');
        $result->assertSee('Kode Klasifikasi');
        $result->assertSee('Bidang');
        $result->assertSee('SPT Terkait');
        $result->assertSee('Tahun Penciptaan');
        $result->assertSee('Kondisi Fisik');

        // 4. JRA Section Placeholder & Read-only Labels
        $result->assertSee('Pilih kode klasifikasi untuk melihat informasi JRA.');
        $result->assertSee('Retensi Aktif (Tahun)');
        $result->assertSee('Retensi Inaktif (Tahun)');
        $result->assertSee('Keterangan Retensi');
        $result->assertSee('Tingkat Keamanan');
        $result->assertSee('Dasar Pertimbangan');

        // 5. Berkas Digital & Watermark
        $result->assertSee('Upload Berkas Digital');
        $result->assertSee('Opsi Keamanan PDF (Watermark)');
        $result->assertSee('Generate Watermark Sistem (Khusus PDF)');

        // 6. Action Buttons
        $result->assertSee('Simpan Arsip');
        $result->assertSee('Batal');

        // 7. Sidebar check (No admin menus)
        $this->assertStringNotContainsString('Manajemen Akun', $body);
        $this->assertStringNotContainsString('Master OPD', $body);
        $this->assertStringNotContainsString('Master Bidang', $body);
        $this->assertStringNotContainsString('Penilaian Arsip', $body);
    }

    public function testArsiparisCanStoreArsip()
    {
        $db = \Config\Database::connect();
        $klas = $db->table('kode_klasifikasi')->get(1)->getRowArray();
        $bidang = $db->table('bidang')->get(1)->getRowArray();

        $nomorArsip = 'TEST/ARSIP/' . time();
        $namaArsip  = 'Dokumen Pengujian Arsip ' . time();

        $postData = [
            'nomor_arsip'         => $nomorArsip,
            'nama_arsip'          => $namaArsip,
            'id_kode_klasifikasi' => $klas['id_kode_klasifikasi'] ?? '97f2537d-9aad-4ee1-a86a-7099274c8ed1',
            'id_bidang'           => $bidang['id_bidang'] ?? 'e372229d-552b-4f99-b006-6334f4bd8d3e',
            'kurun_waktu'         => '2026',
            'kondisi'             => 'Baik',
            'generate_watermark'  => '1',
            'retensi_aktif'       => '5',
        ];

        $result = $this->withSession($this->sessArsiparis())
                       ->post('/arsip/store', $postData);

        $result->assertRedirectTo('/arsip');

        // Verifikasi arsip tersimpan di DB
        $stored = $db->table('arsip')->where('nomor_arsip', $nomorArsip)->get()->getRowArray();
        $this->assertNotNull($stored);
        $this->assertEquals($namaArsip, $stored['nama_arsip']);
        $this->assertEquals('Menunggu', $stored['status_verifikasi']);
        $this->assertEquals('Aktif', $stored['status_retensi_aktif']);
    }
}

