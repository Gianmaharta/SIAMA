<?php

namespace Tests\App\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class PengawasanOpdTest extends CIUnitTestCase
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

    protected function sess(string $uid, string $email, string $nama, string $role, ?string $opd = null): array
    {
        return [
            'user_id'             => $uid,
            'email'               => $email,
            'nama_lengkap'        => $nama,
            'nama_role'           => $role,
            'id_role'             => '',
            'id_opd'              => $opd,
            'id_bidang'           => null,
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];
    }

    protected function setupTestData(): void
    {
        $db = $this->db();
        $db->query('SET FOREIGN_KEY_CHECKS=0');
        $db->query('TRUNCATE TABLE opd');
        $db->query('TRUNCATE TABLE spt');
        $db->query('TRUNCATE TABLE arsip');
        $db->query('SET FOREIGN_KEY_CHECKS=1');

        $tahun = date('Y');

        // Buat OPD
        $db->table('opd')->insert(['id_opd' => 'opd-1', 'nama_opd' => 'Dinas Pendidikan']);
        $db->table('opd')->insert(['id_opd' => 'opd-2', 'nama_opd' => 'Dinas Kesehatan']);

        // Buat SPT (Hanya berpengaruh pada hitungan total SPT, untuk memastikan logic jalan)
        $db->table('spt')->insert(['id_spt' => 'spt-1', 'id_opd' => 'opd-1', 'nomor_spt' => 'SPT/001', 'tanggal_spt' => "$tahun-01-01", 'id_pimpinan' => 'pimp']);

        // OPD 1: 2 arsip target, 1 selesai -> 50%
        $db->table('arsip')->insert(['id_arsip' => 'a1', 'id_opd' => 'opd-1', 'nomor_arsip' => '001', 'nama_arsip' => 'A1', 'status_verifikasi' => 'Menunggu', 'created_at' => "$tahun-01-02 10:00:00", 'id_user_upload' => 'user1']);
        $db->table('arsip')->insert(['id_arsip' => 'a2', 'id_opd' => 'opd-1', 'nomor_arsip' => '002', 'nama_arsip' => 'A2', 'status_verifikasi' => 'Selesai', 'created_at' => "$tahun-01-03 10:00:00", 'id_user_upload' => 'user1']);

        // OPD 2: 1 arsip target, 1 selesai -> 100%
        $db->table('arsip')->insert(['id_arsip' => 'a3', 'id_opd' => 'opd-2', 'nomor_arsip' => '003', 'nama_arsip' => 'A3', 'status_verifikasi' => 'Disetujui', 'created_at' => "$tahun-02-01 10:00:00", 'id_user_upload' => 'user2']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $db = $this->db();
        $db->query('SET FOREIGN_KEY_CHECKS=0');
        $db->query('TRUNCATE TABLE opd');
        $db->query('TRUNCATE TABLE spt');
        $db->query('TRUNCATE TABLE arsip');
        $db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    public function testDashboardPengawasanMenampilkanDataYangBenar(): void
    {
        $this->setupTestData();

        $session = $this->sess('admin-pemkab', 'admin@pemkab.com', 'Admin Pemkab', 'Admin_Pemkab');

        $result = $this->withSession($session)->get('/pengawasan');

        $result->assertStatus(200);

        // Assert perhitungan untuk OPD 1 (50%)
        $result->assertSee('Dinas Pendidikan');
        $result->assertSee('50%'); // Persentase
        $result->assertSee('Baik'); // Status kinerja

        // Assert perhitungan untuk OPD 2 (100%)
        $result->assertSee('Dinas Kesehatan');
        $result->assertSee('100%'); // Persentase
        $result->assertSee('Sangat Baik'); // Status kinerja
    }

    public function testAksesDitolakUntukBukanAdminPemkab(): void
    {
        $session = $this->sess('admin-opd', 'admin@opd.com', 'Admin OPD', 'Admin_OPD');

        $result = $this->withSession($session)->get('/pengawasan');

        // Harus redirect jika filter role jalan
        $result->assertRedirect();
    }
}
