<?php

namespace Tests\App\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\SptModel;
use App\Models\SptAssignmentModel;

class SptTugasAlihMediaOrderTest extends CIUnitTestCase
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

    protected function sess(string $uid, string $email, string $nama, string $role, string $opd = 'opd-1', ?string $bidang = 'bidang-1'): array
    {
        return [
            'user_id'             => $uid,
            'email'               => $email,
            'nama_lengkap'        => $nama,
            'nama_role'           => $role,
            'id_role'             => '',
            'id_opd'              => $opd,
            'id_bidang'           => $bidang,
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $db = $this->db();
        $db->query('SET FOREIGN_KEY_CHECKS=0');
        foreach (['notifications', 'user_roles', 'users', 'opd', 'bidang', 'roles', 'spt', 'spt_assignments', 'arsip', 'berita_acara'] as $t) {
            $db->query("TRUNCATE TABLE `$t`");
        }
        $db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $db = $this->db();
        $db->query('SET FOREIGN_KEY_CHECKS=0');
        foreach (['notifications', 'user_roles', 'users', 'opd', 'bidang', 'roles', 'spt', 'spt_assignments', 'arsip', 'berita_acara'] as $t) {
            $db->query("TRUNCATE TABLE `$t`");
        }
        $db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function setupSeedData(): array
    {
        $db = $this->db();

        // 1. Roles
        $db->table('roles')->insert(['id_role' => 'r-pimpinan',  'nama_role' => 'Pimpinan']);
        $db->table('roles')->insert(['id_role' => 'r-kabid',     'nama_role' => 'Kepala_Bidang']);
        $db->table('roles')->insert(['id_role' => 'r-arsiparis', 'nama_role' => 'Arsiparis']);

        // 2. OPD & Bidang
        $db->table('opd')->insert(['id_opd' => 'opd-1', 'nama_opd' => 'Dinas Komunikasi dan Informatika']);
        $db->table('opd')->insert(['id_opd' => 'opd-2', 'nama_opd' => 'Dinas Pendidikan']);
        $db->table('bidang')->insert(['id_bidang' => 'bidang-1', 'id_opd' => 'opd-1', 'nama_bidang' => 'Bidang TI']);
        $db->table('bidang')->insert(['id_bidang' => 'bidang-2', 'id_opd' => 'opd-2', 'nama_bidang' => 'Bidang Kurikulum']);

        // 3. Users
        $users = [
            ['id_user' => 'u-pimpinan', 'nama' => 'Kepala Dinas Kominfo', 'email' => 'pimpinan@kominfo.test', 'id_opd' => 'opd-1', 'id_bidang' => null, 'r' => 'r-pimpinan'],
            ['id_user' => 'u-kabid-1', 'nama' => 'Kabid Kominfo', 'email' => 'kabid1@kominfo.test', 'id_opd' => 'opd-1', 'id_bidang' => 'bidang-1', 'r' => 'r-kabid'],
            ['id_user' => 'u-kabid-2', 'nama' => 'Kabid Disdik', 'email' => 'kabid2@disdik.test', 'id_opd' => 'opd-2', 'id_bidang' => 'bidang-2', 'r' => 'r-kabid'],
            ['id_user' => 'u-arsiparis-1', 'nama' => 'Arsiparis A', 'email' => 'arsiparisA@kominfo.test', 'id_opd' => 'opd-1', 'id_bidang' => 'bidang-1', 'r' => 'r-arsiparis'],
            ['id_user' => 'u-arsiparis-2', 'nama' => 'Arsiparis B', 'email' => 'arsiparisB@kominfo.test', 'id_opd' => 'opd-1', 'id_bidang' => 'bidang-1', 'r' => 'r-arsiparis'],
        ];

        foreach ($users as $u) {
            $db->table('users')->insert([
                'id_user'             => $u['id_user'],
                'nama'                => $u['nama'],
                'email'               => $u['email'],
                'password'            => password_hash('Pass123!', PASSWORD_DEFAULT),
                'id_opd'              => $u['id_opd'],
                'id_bidang'           => $u['id_bidang'],
                'is_active'           => 1,
                'is_default_password' => 0
            ]);
            $db->table('user_roles')->insert([
                'id_user' => $u['id_user'],
                'id_role' => $u['r']
            ]);
        }

        return [
            'opd_kominfo' => 'opd-1',
            'opd_disdik'  => 'opd-2',
            'pimpinan'    => 'u-pimpinan',
            'kabid_1'     => 'u-kabid-1',
            'kabid_2'     => 'u-kabid-2',
            'arsiparis_1' => 'u-arsiparis-1',
            'arsiparis_2' => 'u-arsiparis-2',
        ];
    }

    public function testKabidSptOrderAndFiltering(): void
    {
        $ids = $this->setupSeedData();
        $db = $this->db();
        $sptModel = new SptModel();

        // 1. Masukkan SPT lama (tanggal 10 Nov)
        $db->table('spt')->insert([
            'id_spt'           => 'spt-lama-1',
            'id_opd'           => $ids['opd_kominfo'],
            'nomor_spt'        => '001/SPT/LAMA/2026',
            'tanggal_spt'      => '2026-11-10',
            'id_pimpinan'      => $ids['pimpinan'],
            'perihal'          => 'Alih Media Arsip Kepegawaian Lama',
            'tanggal_mulai'    => '2026-11-10',
            'tanggal_selesai'  => '2026-11-15',
            'status'           => 'Aktif',
            'status_penugasan' => 'ditugaskan',
            'created_at'       => '2026-11-10 08:00:00'
        ]);

        // 2. Masukkan SPT untuk OPD Disdik (tidak boleh muncul di Kabid Kominfo)
        $db->table('spt')->insert([
            'id_spt'           => 'spt-disdik-1',
            'id_opd'           => $ids['opd_disdik'],
            'nomor_spt'        => '001/SPT/DISDIK/2026',
            'tanggal_spt'      => '2026-11-16',
            'id_pimpinan'      => $ids['pimpinan'],
            'perihal'          => 'Alih Media Ijazah Sekolah',
            'tanggal_mulai'    => '2026-11-16',
            'tanggal_selesai'  => '2026-11-20',
            'status'           => 'Aktif',
            'status_penugasan' => 'belum_ditugaskan',
            'created_at'       => '2026-11-16 09:00:00'
        ]);

        // 3. Masukkan SPT yang baru diterbitkan Pimpinan untuk Kominfo (tanggal 17 Nov jam 10:00:00)
        $db->table('spt')->insert([
            'id_spt'           => 'spt-baru-kominfo',
            'id_opd'           => $ids['opd_kominfo'],
            'nomor_spt'        => '002/SPT/KOMINFO/2026',
            'tanggal_spt'      => '2026-11-17',
            'id_pimpinan'      => $ids['pimpinan'],
            'perihal'          => 'Alih Media Berkas Regulasi Terbaru',
            'tanggal_mulai'    => '2026-11-17',
            'tanggal_selesai'  => '2026-11-25',
            'status'           => 'Aktif',
            'status_penugasan' => 'belum_ditugaskan',
            'created_at'       => '2026-11-17 10:00:00'
        ]);

        // Uji query getSptByOpd untuk Kabid 1 (Kominfo)
        $listKabid1 = $sptModel->getSptByOpd($ids['opd_kominfo']);
        $this->assertCount(2, $listKabid1, 'Kabid Kominfo hanya boleh melihat SPT di OPD Kominfo');

        // Pastikan SPT yang baru diterbitkan berada di urutan PALING ATAS (index 0)
        $this->assertEquals('spt-baru-kominfo', $listKabid1[0]['id_spt'], 'SPT baru harus otomatis berada di urutan paling atas');
        $this->assertEquals('002/SPT/KOMINFO/2026', $listKabid1[0]['nomor_spt']);
        $this->assertEquals('spt-lama-1', $listKabid1[1]['id_spt']);

        // Uji HTTP GET /spt dengan session Kabid Kominfo
        $sessionKabid = $this->sess($ids['kabid_1'], 'kabid1@kominfo.test', 'Kabid Kominfo', 'Kepala_Bidang', $ids['opd_kominfo']);
        $result = $this->withSession($sessionKabid)->get('/spt');
        $result->assertOK();
        $bodyKabid = $result->getBody();
        $this->assertStringContainsString('002/SPT/KOMINFO/2026', $bodyKabid);
        $this->assertStringContainsString('001/SPT/LAMA/2026', $bodyKabid);
        $this->assertStringNotContainsString('001/SPT/DISDIK/2026', $bodyKabid, 'SPT OPD lain tidak boleh tampil');

        // Setelah Kabid menindaklanjuti (update status_penugasan ke ditugaskan), data tetap ada & tetap paling atas
        $sptModel->update('spt-baru-kominfo', ['status_penugasan' => 'ditugaskan']);
        $listAfterAction = $sptModel->getSptByOpd($ids['opd_kominfo']);
        $this->assertCount(2, $listAfterAction);
        $this->assertEquals('spt-baru-kominfo', $listAfterAction[0]['id_spt'], 'Setelah ditindaklanjuti, SPT tetap ada dan tetap paling atas berdasarkan waktu terbit');
    }

    public function testArsiparisTugasAlihMediaOrderAndIsolation(): void
    {
        $ids = $this->setupSeedData();
        $db = $this->db();
        $sptModel = new SptModel();

        // Siapkan 2 SPT di DB
        $db->table('spt')->insert([
            'id_spt'           => 'spt-tugas-1',
            'id_opd'           => $ids['opd_kominfo'],
            'nomor_spt'        => '010/SPT/KOMINFO/2026',
            'tanggal_spt'      => '2026-11-12',
            'id_pimpinan'      => $ids['pimpinan'],
            'perihal'          => 'Pekerjaan Alih Media Tahap I',
            'tanggal_mulai'    => '2026-11-12',
            'tanggal_selesai'  => '2026-11-18',
            'status'           => 'Aktif',
            'status_penugasan' => 'ditugaskan'
        ]);

        $db->table('spt')->insert([
            'id_spt'           => 'spt-tugas-2',
            'id_opd'           => $ids['opd_kominfo'],
            'nomor_spt'        => '020/SPT/KOMINFO/2026',
            'tanggal_spt'      => '2026-11-15',
            'id_pimpinan'      => $ids['pimpinan'],
            'perihal'          => 'Pekerjaan Alih Media Tahap II Prioritas',
            'tanggal_mulai'    => '2026-11-15',
            'tanggal_selesai'  => '2026-11-25',
            'status'           => 'Aktif',
            'status_penugasan' => 'ditugaskan'
        ]);

        $db->table('spt')->insert([
            'id_spt'           => 'spt-tugas-arsiparis2',
            'id_opd'           => $ids['opd_kominfo'],
            'nomor_spt'        => '030/SPT/KOMINFO/2026',
            'tanggal_spt'      => '2026-11-16',
            'id_pimpinan'      => $ids['pimpinan'],
            'perihal'          => 'Tugas Khusus Arsiparis B',
            'tanggal_mulai'    => '2026-11-16',
            'tanggal_selesai'  => '2026-11-20',
            'status'           => 'Aktif',
            'status_penugasan' => 'ditugaskan'
        ]);

        // 1. Penugasan 1 ke Arsiparis A pada jam 08:00
        $db->table('spt_assignments')->insert([
            'id_assignment' => 'assign-1',
            'id_spt'        => 'spt-tugas-1',
            'id_user'       => $ids['arsiparis_1'],
            'assigned_by'   => $ids['kabid_1'],
            'status'        => 'pending',
            'created_at'    => '2026-11-15 08:00:00'
        ]);

        // 2. Penugasan ke Arsiparis B
        $db->table('spt_assignments')->insert([
            'id_assignment' => 'assign-b',
            'id_spt'        => 'spt-tugas-arsiparis2',
            'id_user'       => $ids['arsiparis_2'],
            'assigned_by'   => $ids['kabid_1'],
            'status'        => 'pending',
            'created_at'    => '2026-11-16 09:00:00'
        ]);

        // 3. Penugasan BARU ke Arsiparis A pada jam 14:00 (harus otomatis berada paling atas untuk Arsiparis A)
        $db->table('spt_assignments')->insert([
            'id_assignment' => 'assign-2',
            'id_spt'        => 'spt-tugas-2',
            'id_user'       => $ids['arsiparis_1'],
            'assigned_by'   => $ids['kabid_1'],
            'status'        => 'pending',
            'created_at'    => '2026-11-17 14:00:00'
        ]);

        // Uji query getSptByPelaksana untuk Arsiparis A
        $listArsiparisA = $sptModel->getSptByPelaksana($ids['arsiparis_1']);
        $this->assertCount(2, $listArsiparisA, 'Hanya tugas yang ditugaskan ke Arsiparis A yang boleh tampil');

        // Pastikan tugas baru berada di urutan PALING ATAS
        $this->assertEquals('spt-tugas-2', $listArsiparisA[0]['id_spt']);
        $this->assertEquals('020/SPT/KOMINFO/2026', $listArsiparisA[0]['nomor_spt']);
        $this->assertEquals('2026-11-17 14:00:00', $listArsiparisA[0]['tanggal_penugasan']);

        $this->assertEquals('spt-tugas-1', $listArsiparisA[1]['id_spt']);

        // Uji HTTP GET /spt dengan session Arsiparis A
        $sessionArsiparisA = $this->sess($ids['arsiparis_1'], 'arsiparisA@kominfo.test', 'Arsiparis A', 'Arsiparis', $ids['opd_kominfo']);
        $resultA = $this->withSession($sessionArsiparisA)->get('/spt');
        $resultA->assertOK();
        $bodyA = $resultA->getBody();
        $this->assertStringContainsString('Tugas Alih Media', $bodyA);
        $this->assertStringContainsString('020/SPT/KOMINFO/2026', $bodyA);
        $this->assertStringContainsString('010/SPT/KOMINFO/2026', $bodyA);
        $this->assertStringNotContainsString('030/SPT/KOMINFO/2026', $bodyA, 'Tugas Arsiparis B tidak boleh bocor ke Arsiparis A');

        // Uji bahwa setelah status tugas berubah ke 'selesai', data tetap tersimpan dan tidak hilang
        $db->table('spt_assignments')->where('id_assignment', 'assign-2')->update(['status' => 'selesai']);
        $listAfterSelesai = $sptModel->getSptByPelaksana($ids['arsiparis_1']);
        $this->assertCount(2, $listAfterSelesai, 'Tugas yang telah selesai tetap disimpan dan tampil dalam riwayat');
        $this->assertEquals('spt-tugas-2', $listAfterSelesai[0]['id_spt'], 'Urutan tetap konsisten mengikuti waktu penugasan');
    }
}
