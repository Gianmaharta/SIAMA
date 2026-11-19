<?php

namespace Tests\App\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Services\DataCleanseService;

class DataCleanseFeatureTest extends CIUnitTestCase
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

    protected function sess(string $uid, string $email, string $nama, string $role, ?string $opd = null, ?string $bidang = null): array
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
        foreach (['notifications', 'user_roles', 'users', 'opd', 'bidang', 'roles', 'spt', 'spt_assignments', 'arsip', 'berita_acara', 'berita_acara_detail', 'activity_log', 'access_log', 'kode_klasifikasi', 'jra'] as $t) {
            $db->query("TRUNCATE TABLE `$t`");
        }
        $db->query('SET FOREIGN_KEY_CHECKS=1');

        $this->seedTestData();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $db = $this->db();
        $db->query('SET FOREIGN_KEY_CHECKS=0');
        foreach (['notifications', 'user_roles', 'users', 'opd', 'bidang', 'roles', 'spt', 'spt_assignments', 'arsip', 'berita_acara', 'berita_acara_detail', 'activity_log', 'access_log', 'kode_klasifikasi', 'jra'] as $t) {
            $db->query("TRUNCATE TABLE `$t`");
        }
        $db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function seedTestData(): void
    {
        $db = $this->db();

        // 1. Roles
        $db->table('roles')->insertBatch([
            ['id_role' => 'r-admin-pemkab', 'nama_role' => 'Admin_Pemkab'],
            ['id_role' => 'r-admin-opd',    'nama_role' => 'Admin_OPD'],
            ['id_role' => 'r-pimpinan',     'nama_role' => 'Pimpinan'],
            ['id_role' => 'r-kabid',        'nama_role' => 'Kepala_Bidang'],
            ['id_role' => 'r-arsiparis',    'nama_role' => 'Arsiparis'],
        ]);

        // 2. OPD & Bidang
        $db->table('opd')->insert([
            'id_opd'   => 'opd-1',
            'kode_opd' => 'OPD-001',
            'nama_opd' => 'Dinas Kearsipan Test',
        ]);
        $db->table('bidang')->insert([
            'id_bidang'   => 'bidang-1',
            'id_opd'      => 'opd-1',
            'nama_bidang' => 'Bidang Pengelolaan Arsip',
        ]);

        // 3. Klasifikasi
        $db->table('kode_klasifikasi')->insert([
            'id_kode_klasifikasi' => 'klas-1',
            'kode'                => '000',
            'nama_klasifikasi'    => 'Umum',
        ]);

        // 4. Admin Pemkab Utama
        $db->table('users')->insert([
            'id_user'   => 'u-admin-pemkab',
            'nama'      => 'Administrator Pemkab',
            'email'     => 'admin.pemkab@siama.test',
            'password'  => password_hash('Admin123!', PASSWORD_DEFAULT),
            'id_opd'    => null,
            'is_active' => 1,
        ]);
        $db->table('user_roles')->insert([
            'id_user' => 'u-admin-pemkab',
            'id_role' => 'r-admin-pemkab',
        ]);

        // 5. Arsiparis User
        $db->table('users')->insert([
            'id_user'   => 'u-arsiparis',
            'nama'      => 'Arsiparis User',
            'email'     => 'arsiparis@siama.test',
            'password'  => password_hash('Admin123!', PASSWORD_DEFAULT),
            'id_opd'    => 'opd-1',
            'is_active' => 1,
        ]);
        $db->table('user_roles')->insert([
            'id_user' => 'u-arsiparis',
            'id_role' => 'r-arsiparis',
        ]);

        // 6. Transaksi: SPT, Arsip, BA, Notif, Log
        $db->table('spt')->insert([
            'id_spt'          => 'spt-test-1',
            'id_opd'          => 'opd-1',
            'nomor_spt'       => '001/SPT/2026',
            'tanggal_spt'     => '2026-11-01',
            'id_pimpinan'     => 'u-admin-pemkab',
            'perihal'         => 'Test SPT Cleansing',
            'tanggal_mulai'   => '2026-11-01',
            'tanggal_selesai' => '2026-11-30',
            'status'          => 'Aktif',
        ]);

        $db->table('spt_assignments')->insert([
            'id_assignment' => 'assign-1',
            'id_spt'        => 'spt-test-1',
            'id_user'       => 'u-arsiparis',
            'assigned_by'   => 'u-admin-pemkab',
            'status'        => 'proses',
        ]);

        $db->table('arsip')->insert([
            'id_arsip'          => 'arsip-1',
            'id_spt'            => 'spt-test-1',
            'id_opd'            => 'opd-1',
            'nomor_arsip'       => '001/ARS/2026',
            'nama_arsip'        => 'Berkas Uji Coba',
            'id_user_upload'    => 'u-arsiparis',
            'status_verifikasi' => 'Selesai',
        ]);

        $db->table('berita_acara')->insert([
            'id_berita_acara'    => 'ba-1',
            'id_opd'             => 'opd-1',
            'id_spt'             => 'spt-test-1',
            'nomor_ba'           => '001/BA/2026',
            'tanggal_ba'         => '2026-11-02',
            'status_persetujuan' => 'Disetujui',
            'id_pelaksana'       => 'u-arsiparis',
        ]);

        $db->table('berita_acara_detail')->insert([
            'id_berita_acara' => 'ba-1',
            'id_arsip'        => 'arsip-1',
        ]);

        $db->table('notifications')->insert([
            'id_notification' => 'notif-1',
            'id_user'         => 'u-arsiparis',
            'title'           => 'Tugas Baru',
            'message'         => 'Pesan notifikasi',
            'is_read'         => 0,
        ]);

        $db->table('activity_log')->insert([
            'id'          => 'log-1',
            'user_id'     => 'u-admin-pemkab',
            'module'      => 'SPT',
            'action'      => 'CREATE',
            'description' => 'Membuat SPT baru',
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        $db->table('access_log')->insert([
            'id'         => 'access-1',
            'user_id'    => 'u-admin-pemkab',
            'event'      => 'LOGIN',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function testDataCleanseServiceClearsTransactionsAndKeepsMasters(): void
    {
        $service = new DataCleanseService();

        // 1. Ringkasan awal harus mendeteksi data transaksi
        $summary = $service->getSummary();
        $this->assertGreaterThan(0, $summary['total_transactions']);
        $this->assertEquals(2, $summary['masters']['users']);
        $this->assertEquals(1, $summary['masters']['opd']);

        // 2. Eksekusi cleanse (skip physical backup during unit test)
        $result = $service->executeCleanse(true);
        $this->assertEquals('success', $result['status']);

        // 3. Validasi bahwa data transaksi kini 0
        $validation = $service->validateCleanState();
        $this->assertTrue($validation['is_all_valid']);

        $db = $this->db();
        $this->assertEquals(0, $db->table('spt')->countAllResults());
        $this->assertEquals(0, $db->table('spt_assignments')->countAllResults());
        $this->assertEquals(0, $db->table('arsip')->countAllResults());
        $this->assertEquals(0, $db->table('berita_acara')->countAllResults());
        $this->assertEquals(0, $db->table('berita_acara_detail')->countAllResults());
        $this->assertEquals(0, $db->table('notifications')->countAllResults());
        $this->assertEquals(0, $db->table('activity_log')->countAllResults());
        $this->assertEquals(0, $db->table('access_log')->countAllResults());

        // 4. Data Master & Admin Pemkab tetap utuh
        $this->assertEquals(1, $db->table('opd')->countAllResults());
        $this->assertEquals(1, $db->table('bidang')->countAllResults());
        $this->assertEquals(1, $db->table('kode_klasifikasi')->countAllResults());
        $this->assertEquals(5, $db->table('roles')->countAllResults());

        $admin = $db->table('users')->where('email', 'admin.pemkab@siama.test')->get()->getRowArray();
        $this->assertNotNull($admin);
        $this->assertEquals(1, (int)$admin['is_active']);
    }

    public function testAccessControlRoleRestrictions(): void
    {
        $sessionPemkab = $this->sess('u-admin-pemkab', 'admin.pemkab@siama.test', 'Admin Pemkab', 'Admin_Pemkab');
        $sessionArsiparis = $this->sess('u-arsiparis', 'arsiparis@siama.test', 'Arsiparis', 'Arsiparis', 'opd-1');

        // Admin Pemkab: Berhasil akses halaman data cleanse
        $resPemkab = $this->withSession($sessionPemkab)->get('/system-cleanse');
        $resPemkab->assertOK();
        $resPemkab->assertSee('Reset Seluruh Data Operasional');

        // Role selain Admin_Pemkab (misal Arsiparis): Ditolak redirect/forbidden
        $resArsiparis = $this->withSession($sessionArsiparis)->get('/system-cleanse');
        $resArsiparis->assertRedirect();
    }

    public function testAllRoleDashboardsAndModulesInCleanState(): void
    {
        // 1. Jalankan cleanse terlebih dahulu
        $service = new DataCleanseService();
        $service->executeCleanse(true);

        $sessionPemkab = $this->sess('u-admin-pemkab', 'admin.pemkab@siama.test', 'Admin Pemkab', 'Admin_Pemkab');
        $sessionPimpinan = $this->sess('u-pimpinan', 'pimpinan@siama.test', 'Kepala Dinas', 'Pimpinan', 'opd-1');
        $sessionKabid = $this->sess('u-kabid', 'kabid@siama.test', 'Kepala Bidang', 'Kepala_Bidang', 'opd-1', 'bidang-1');
        $sessionArsiparis = $this->sess('u-arsiparis', 'arsiparis@siama.test', 'Arsiparis User', 'Arsiparis', 'opd-1', 'bidang-1');
        $sessionAdminOpd = $this->sess('u-admin-opd', 'admin.opd@siama.test', 'Admin OPD', 'Admin_OPD', 'opd-1');

        // A. Validasi Dashboard Seluruh 5 Role
        $this->withSession($sessionPemkab)->get('/dashboard')->assertOK();
        $this->withSession($sessionPimpinan)->get('/dashboard')->assertOK();
        $this->withSession($sessionKabid)->get('/dashboard')->assertOK();
        $this->withSession($sessionArsiparis)->get('/dashboard')->assertOK();
        $this->withSession($sessionAdminOpd)->get('/dashboard')->assertOK();

        // B. Validasi Halaman Operasional dalam Kondisi Kosong
        // 1. SPT (Kabid & Arsiparis)
        $resSpt = $this->withSession($sessionKabid)->get('/spt');
        $resSpt->assertOK();

        // 2. Arsip (Arsiparis)
        $resArsip = $this->withSession($sessionArsiparis)->get('/arsip');
        $resArsip->assertOK();

        // 3. Berita Acara (Arsiparis & Kabid)
        $resBa = $this->withSession($sessionKabid)->get('/berita-acara');
        $resBa->assertOK();

        // 4. Notifikasi
        $resNotif = $this->withSession($sessionArsiparis)->get('/notification');
        $resNotif->assertOK();
        $resNotif->assertSee('Tidak Ada Notifikasi');

        // 5. Audit Log (Admin Pemkab)
        $resAccess = $this->withSession($sessionPemkab)->get('/audit-log/access');
        $resAccess->assertOK();
        $resActivity = $this->withSession($sessionPemkab)->get('/audit-log/activity');
        $resActivity->assertOK();
    }
}
