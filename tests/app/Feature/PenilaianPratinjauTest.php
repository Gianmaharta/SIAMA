<?php

namespace Tests\App\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class PenilaianPratinjauTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $DBGroup     = 'tests';
    protected $migrate     = false;
    protected $migrateOnce = false;
    protected $refresh     = false;
    protected $seed        = '';

    protected function db()
    {
        return \Config\Database::connect('tests');
    }

    protected function sessAdminPemkab(): array
    {
        return [
            'user_id'             => 'user-admin-pemkab',
            'email'               => 'admin.pemkab@siama.test',
            'nama_lengkap'        => 'Admin Pemkab',
            'nama_role'           => 'Admin_Pemkab',
            'id_role'             => '1',
            'id_opd'              => null,
            'id_bidang'           => null,
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $db = $this->db();
        $db->query('SET FOREIGN_KEY_CHECKS=0');
        $db->table('arsip')->truncate();
        $db->table('spt')->truncate();
        $db->table('opd')->truncate();
        $db->table('users')->truncate();
        $db->table('roles')->truncate();
        $db->table('user_roles')->truncate();
        $db->query('SET FOREIGN_KEY_CHECKS=1');

        $db->table('roles')->insert([
            'id_role'   => '1',
            'nama_role' => 'Admin_Pemkab',
        ]);

        $db->table('users')->insert([
            'id_user'             => 'user-admin-pemkab',
            'nama'                => 'Admin Pemkab',
            'email'               => 'admin.pemkab@siama.test',
            'password'            => password_hash('Pass123!', PASSWORD_DEFAULT),
            'is_active'           => 1,
            'is_default_password' => 0,
        ]);

        $db->table('user_roles')->insert([
            'id_user' => 'user-admin-pemkab',
            'id_role' => '1',
        ]);

        $db->table('opd')->insert([
            'id_opd'   => 'opd-test-1',
            'nama_opd' => 'Dinas Komunikasi dan Informatika',
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $db = $this->db();
        $db->query('SET FOREIGN_KEY_CHECKS=0');
        $db->table('arsip')->truncate();
        $db->table('spt')->truncate();
        $db->table('opd')->truncate();
        $db->table('users')->truncate();
        $db->table('roles')->truncate();
        $db->table('user_roles')->truncate();
        $db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    public function testPratinjauDisplaysAlihMediaFileWhenPresentAndPhysicalExists(): void
    {
        $db = $this->db();
        $testFileName = 'test_alih_media_' . time() . '.pdf';
        $fullPath = FCPATH . 'uploads/arsip/' . $testFileName;
        file_put_contents($fullPath, '%PDF-1.4 Dummy Alih Media Scan Content');

        $idArsip = 'arsip-test-ready';
        $db->table('arsip')->insert([
            'id_arsip'          => $idArsip,
            'id_opd'            => 'opd-test-1',
            'nomor_arsip'       => 'ARSIP/001/TEST/2026',
            'nama_arsip'        => 'Berkas Surat Keputusan Kegiatan',
            'file_arsip'        => $testFileName,
            'id_user_upload'    => 'user-admin-pemkab',
            'status_verifikasi' => 'Selesai',
            'status_autentikasi'=> 'Belum Dinilai',
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        try {
            $result = $this->withSession($this->sessAdminPemkab())->get('penilaian/form/' . $idArsip);
            $result->assertStatus(200);
            $result->assertSee('Pratinjau Dokumen');
            $result->assertSee($testFileName);
            $result->assertSee('Buka di Tab Baru');
            $result->assertDontSee('Dokumen hasil alih media belum tersedia.');
        } finally {
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    public function testPratinjauShowsNotAvailableMessageWhenNoFileUploadedAndNeverUsesSptSource(): void
    {
        $db = $this->db();
        // Buat SPT dengan file dokumen sumber SPT
        $idSpt = 'spt-test-1';
        $sourceFileName = 'spt_source_document.pdf';
        $db->table('spt')->insert([
            'id_spt'            => $idSpt,
            'id_opd'            => 'opd-test-1',
            'nomor_spt'         => 'SPT/2026/001',
            'perihal'           => 'Perintah Alih Media Arsip',
            'tanggal_spt'       => date('Y-m-d'),
            'file_spt'          => $sourceFileName,
            'status'            => 'aktif',
            'status_penugasan'  => 'ditugaskan',
        ]);

        // Buat arsip yang terhubung ke SPT tersebut tetapi BELUM dialihmediakan (file_arsip NULL)
        $idArsip = 'arsip-test-empty';
        $db->table('arsip')->insert([
            'id_arsip'          => $idArsip,
            'id_spt'            => $idSpt,
            'id_opd'            => 'opd-test-1',
            'nomor_arsip'       => 'ARSIP/002/EMPTY/2026',
            'nama_arsip'        => 'Berkas Belum Dialihmediakan',
            'file_arsip'        => null,
            'id_user_upload'    => 'user-admin-pemkab',
            'status_verifikasi' => 'Menunggu',
            'status_autentikasi'=> 'Belum Dinilai',
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        $result = $this->withSession($this->sessAdminPemkab())->get('penilaian/form/' . $idArsip);
        $result->assertStatus(200);
        $result->assertSee('Pratinjau Dokumen');
        // Harus menampilkan pesan sesuai instruksi
        $result->assertSee('Dokumen hasil alih media belum tersedia.');
        // Tombol Buka di Tab Baru tidak boleh ada
        $result->assertDontSee('Buka di Tab Baru');
        // File dokumen sumber SPT TIDAK BOLEH muncul sebagai pengganti hasil alih media
        $result->assertDontSee($sourceFileName);
    }

    public function testPratinjauShowsNotAvailableMessageWhenFileRecordedInDbButMissingOnDisk(): void
    {
        $db = $this->db();
        $idArsip = 'arsip-test-missing-file';
        $db->table('arsip')->insert([
            'id_arsip'          => $idArsip,
            'id_opd'            => 'opd-test-1',
            'nomor_arsip'       => 'ARSIP/003/MISSING/2026',
            'nama_arsip'        => 'Berkas Hilang Dari Storage',
            'file_arsip'        => 'ghost_file_not_found.pdf',
            'id_user_upload'    => 'user-admin-pemkab',
            'status_verifikasi' => 'Menunggu',
            'status_autentikasi'=> 'Belum Dinilai',
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        $result = $this->withSession($this->sessAdminPemkab())->get('penilaian/form/' . $idArsip);
        $result->assertStatus(200);
        $result->assertSee('Pratinjau Dokumen');
        $result->assertSee('Dokumen hasil alih media belum tersedia.');
        $result->assertDontSee('Buka di Tab Baru');
    }

    public function testPenilaianDetailShowsNotAvailableMessageWhenNoFile(): void
    {
        $db = $this->db();
        $idArsip = 'arsip-test-detail-empty';
        $db->table('arsip')->insert([
            'id_arsip'          => $idArsip,
            'id_opd'            => 'opd-test-1',
            'nomor_arsip'       => 'ARSIP/004/DETAIL/2026',
            'nama_arsip'        => 'Berkas Detail Belum Ada',
            'file_arsip'        => null,
            'id_user_upload'    => 'user-admin-pemkab',
            'status_verifikasi' => 'Menunggu',
            'status_autentikasi'=> 'Belum Dinilai',
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        $result = $this->withSession($this->sessAdminPemkab())->get('penilaian/detail/' . $idArsip);
        $result->assertStatus(200);
        $result->assertSee('Dokumen hasil alih media belum tersedia.');
    }
}
