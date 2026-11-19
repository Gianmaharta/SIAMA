<?php

namespace Tests\App\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Test Switch Role / User Impersonation (Admin Pemkab)
 */
class SwitchRoleTest extends CIUnitTestCase
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

    protected function setUp(): void
    {
        parent::setUp();
        $db = $this->db();
        $db->query('SET FOREIGN_KEY_CHECKS=0');
        foreach (['user_roles', 'users', 'opd', 'roles'] as $t) {
            $db->query("TRUNCATE TABLE `$t`");
        }
        $db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function setupTestData(): array
    {
        $db = $this->db();

        // Buat role
        $db->table('roles')->insert(['id_role' => 'r-admin',     'nama_role' => 'Admin_Pemkab']);
        $db->table('roles')->insert(['id_role' => 'r-opd',       'nama_role' => 'Admin_OPD']);
        $db->table('roles')->insert(['id_role' => 'r-arsiparis',  'nama_role' => 'Arsiparis']);
        $db->table('roles')->insert(['id_role' => 'r-kabid',      'nama_role' => 'Kepala_Bidang']);
        $db->table('roles')->insert(['id_role' => 'r-pimpinan',   'nama_role' => 'Pimpinan']);

        // Buat OPD
        $db->table('opd')->insert(['id_opd' => 'opd-test', 'nama_opd' => 'Dinas Pertanian, Ketahanan Pangan dan Perikanan']);

        // Buat user Admin Pemkab
        $db->table('users')->insert([
            'id_user' => 'admin-pemkab-1', 'nama' => 'Admin Pemkab Test',
            'email' => 'admin@switch.test', 'password' => password_hash('Test123!', PASSWORD_DEFAULT),
            'id_opd' => null, 'is_active' => 1, 'is_default_password' => 0,
        ]);
        $db->table('user_roles')->insert(['id_user' => 'admin-pemkab-1', 'id_role' => 'r-admin']);

        // Buat user Admin OPD
        $db->table('users')->insert([
            'id_user' => 'admin-opd-1', 'nama' => 'Admin OPD Target',
            'email' => 'opd@switch.test', 'password' => password_hash('Test123!', PASSWORD_DEFAULT),
            'id_opd' => 'opd-test', 'is_active' => 1, 'is_default_password' => 0,
        ]);
        $db->table('user_roles')->insert(['id_user' => 'admin-opd-1', 'id_role' => 'r-opd']);

        // Buat user Arsiparis
        $db->table('users')->insert([
            'id_user' => 'arsiparis-1', 'nama' => 'Ni Luh Ayu (Arsiparis)',
            'email' => 'arsiparis@switch.test', 'password' => password_hash('Test123!', PASSWORD_DEFAULT),
            'id_opd' => 'opd-test', 'is_active' => 1, 'is_default_password' => 0,
        ]);
        $db->table('user_roles')->insert(['id_user' => 'arsiparis-1', 'id_role' => 'r-arsiparis']);

        // Buat user Kepala Bidang
        $db->table('users')->insert([
            'id_user' => 'kabid-1', 'nama' => 'Ketut Sujana',
            'email' => 'kabid@switch.test', 'password' => password_hash('Test123!', PASSWORD_DEFAULT),
            'id_opd' => 'opd-test', 'is_active' => 1, 'is_default_password' => 0,
        ]);
        $db->table('user_roles')->insert(['id_user' => 'kabid-1', 'id_role' => 'r-kabid']);

        // Buat user Pimpinan
        $db->table('users')->insert([
            'id_user' => 'pimpinan-1', 'nama' => 'I Made Pimpinan',
            'email' => 'pimpinan@switch.test', 'password' => password_hash('Test123!', PASSWORD_DEFAULT),
            'id_opd' => 'opd-test', 'is_active' => 1, 'is_default_password' => 0,
        ]);
        $db->table('user_roles')->insert(['id_user' => 'pimpinan-1', 'id_role' => 'r-pimpinan']);

        return [
            'admin_id'    => 'admin-pemkab-1',
            'target_id'   => 'admin-opd-1',
            'arsiparis_id'=> 'arsiparis-1',
            'kabid_id'    => 'kabid-1',
            'pimpinan_id' => 'pimpinan-1',
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $db = $this->db();
        $db->query('SET FOREIGN_KEY_CHECKS=0');
        foreach (['user_roles', 'users', 'opd', 'roles'] as $t) {
            $db->query("TRUNCATE TABLE `$t`");
        }
        $db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    public function testSwitchUserSebagaiAdminPemkab(): void
    {
        $ids = $this->setupTestData();

        // Login sebagai Admin Pemkab
        $result = $this->withSession($this->sess($ids['admin_id'], 'admin@switch.test', 'Admin Pemkab Test', 'Admin_Pemkab'))
            ->get('/users/switch/' . $ids['target_id']);

        // Harus redirect ke dashboard
        $result->assertRedirect();

        // Session harus berubah ke target user
        $this->assertEquals('admin-opd-1', session()->get('user_id'));
        $this->assertEquals('Admin_OPD', session()->get('nama_role'));

        // Session admin asli harus tersimpan
        $this->assertEquals('admin-pemkab-1', session()->get('original_admin_id'));
    }

    public function testSwitchBackKeAdminPemkab(): void
    {
        $ids = $this->setupTestData();

        // Simulasi sudah dalam mode impersonation
        $switchedSession = $this->sess($ids['target_id'], 'opd@switch.test', 'Admin OPD Target', 'Admin_OPD', 'opd-test');
        $switchedSession['original_admin_id']    = $ids['admin_id'];
        $switchedSession['original_admin_email']  = 'admin@switch.test';
        $switchedSession['original_admin_nama']   = 'Admin Pemkab Test';
        $switchedSession['original_admin_role']   = 'Admin_Pemkab';
        $switchedSession['original_admin_id_role'] = 'r-admin';
        $switchedSession['original_admin_id_opd']  = null;
        $switchedSession['original_admin_id_bidang'] = null;

        $result = $this->withSession($switchedSession)->get('/users/switch-back');

        // Harus redirect ke dashboard
        $result->assertRedirect();

        // Session harus kembali ke Admin Pemkab asli
        $this->assertEquals('admin-pemkab-1', session()->get('user_id'));
        $this->assertEquals('Admin_Pemkab', session()->get('nama_role'));

        // Data impersonation harus terhapus
        $this->assertNull(session()->get('original_admin_id'));
    }

    public function testNonAdminTidakBisaSwitch(): void
    {
        $ids = $this->setupTestData();

        // Login sebagai Admin OPD (bukan Admin Pemkab)
        $result = $this->withSession($this->sess($ids['target_id'], 'opd@switch.test', 'Admin OPD', 'Admin_OPD', 'opd-test'))
            ->get('/users/switch/' . $ids['admin_id']);

        // Harus redirect ke dashboard dengan error
        $result->assertRedirectTo('/dashboard');
        $this->assertEquals('Akses Ditolak. Fitur Masuk Sebagai hanya dapat digunakan oleh Admin Pemkab.', session()->getFlashdata('error'));
    }

    public function testAdminOpdCannotSeeSwitchModalOrButton(): void
    {
        $ids = $this->setupTestData();

        // Login sebagai Admin OPD
        $result = $this->withSession($this->sess($ids['target_id'], 'opd@switch.test', 'Admin OPD', 'Admin_OPD', 'opd-test'))
            ->get('/users');

        $result->assertOK();
        $body = $result->getBody();

        // Modal switch user tidak boleh dirender untuk Admin OPD
        $this->assertStringNotContainsString('id="modalSwitchUser"', $body);
        $this->assertStringNotContainsString('id="btnConfirmSwitchUser"', $body);
        $this->assertStringNotContainsString('Masuk Sebagai Pengguna?', $body);

        // Flag isCanSwitch harus false
        $this->assertStringContainsString('const isCanSwitch = false;', $body);
    }

    public function testImpersonatingAdminOpdCannotSeeSwitchModalOrButton(): void
    {
        $ids = $this->setupTestData();

        // Admin Pemkab switch ke Admin OPD
        $this->withSession($this->sess($ids['admin_id'], 'admin@switch.test', 'Admin Pemkab Test', 'Admin_Pemkab'))
            ->get('/users/switch/' . $ids['target_id']);

        $switchedSession = session()->get();
        $this->assertEquals('Admin_OPD', $switchedSession['nama_role']);
        $this->assertTrue(isset($switchedSession['original_admin_id']));

        // Saat membuka /users dalam mode Admin OPD (walaupun sedang impersonate):
        $result = $this->withSession($switchedSession)->get('/users');
        $result->assertOK();
        $body = $result->getBody();

        // Modal switch user dan tombol "Masuk Sebagai" TIDAK BOLEH MUNCUL
        $this->assertStringNotContainsString('id="modalSwitchUser"', $body);
        $this->assertStringNotContainsString('id="btnConfirmSwitchUser"', $body);
        $this->assertStringNotContainsString('Masuk Sebagai Pengguna?', $body);
        $this->assertStringContainsString('const isCanSwitch = false;', $body);

        // Jika mencoba switch lagi saat sudah dalam penyamaran, harus ditolak
        $switchAgain = $this->withSession($switchedSession)->get('/users/switch/' . $ids['arsiparis_id']);
        $switchAgain->assertRedirectTo('/dashboard');
        $this->assertEquals('Akses Ditolak. Fitur Masuk Sebagai hanya dapat digunakan oleh Admin Pemkab.', session()->getFlashdata('error'));
    }

    public function testModalSwitchUserRendersCorrectly(): void
    {
        $ids = $this->setupTestData();

        $result = $this->withSession($this->sess($ids['admin_id'], 'admin@switch.test', 'Admin Pemkab Test', 'Admin_Pemkab'))
            ->get('/users');

        $result->assertOK();
        $body = $result->getBody();
        $result->assertSee('Masuk Sebagai Pengguna?');
        $result->assertSee('Anda akan dialihkan');
        $this->assertStringContainsString('id="switchUserName"', $body);
        $this->assertStringContainsString('id="switchUserNameRole"', $body);
        $this->assertStringContainsString('id="switchUserEmail"', $body);
        $this->assertStringContainsString('id="switchUserRole"', $body);
        $this->assertStringContainsString('id="switchUserOpd"', $body);
        $result->assertSee('Masuk Sekarang');
        $result->assertSee('Batal');
    }

    public function testImpersonateArsiparisSidebarAndAccessControl(): void
    {
        $ids = $this->setupTestData();

        // 1. Admin Pemkab switch ke Arsiparis
        $switchRes = $this->withSession($this->sess($ids['admin_id'], 'admin@switch.test', 'Admin Pemkab Test', 'Admin_Pemkab'))
            ->get('/users/switch/' . $ids['arsiparis_id']);
        $switchRes->assertRedirectTo('/dashboard');

        // 2. Akses Dashboard dengan session impersonate Arsiparis
        $sessionData = session()->get();
        $dashRes = $this->withSession($sessionData)->get('/dashboard');
        $dashRes->assertOK();

        // Banner harus tampil
        $dashRes->assertSee('Sedang Masuk sebagai Ni Luh Ayu (Arsiparis)');
        $dashRes->assertDontSee('Ni Luh Ayu (Arsiparis) (Arsiparis)');
        $dashRes->assertDontSee('Mode Penyamaran Aktif');
        $dashRes->assertSee('Dinas Pertanian, Ketahanan Pangan dan Perikanan.');
        $dashRes->assertSee('Kembali ke Akun Admin Pemkab');

        // Sidebar harus mengikuti role Arsiparis
        $dashRes->assertSee('sidebar-menu-pimpinan');
        $dashRes->assertSee('Surat Perintah Tugas (SPT)');
        $dashRes->assertSee('Pengelolaan Arsip');
        $dashRes->assertSee('Berita Acara');

        // Menu di luar hak akses Arsiparis TIDAK boleh tampil di sidebar
        $dashRes->assertDontSee('Master OPD');
        $dashRes->assertDontSee('Master Bidang');
        $dashRes->assertDontSee('Penilaian Arsip');
        $dashRes->assertDontSee('Manajemen Akun');
        $dashRes->assertDontSee('Access Log');
        $dashRes->assertDontSee('Activity Log');

        // 3. Mengetik URL di luar hak akses harus diblokir oleh RoleFilter
        $opdDirect = $this->withSession($sessionData)->get('/opd');
        $opdDirect->assertRedirectTo('/dashboard');

        $usersDirect = $this->withSession($sessionData)->get('/users');
        $usersDirect->assertRedirectTo('/dashboard');
    }

    public function testImpersonateKepalaBidangSidebarAndAccessControl(): void
    {
        $ids = $this->setupTestData();

        // Switch ke Kepala Bidang
        $this->withSession($this->sess($ids['admin_id'], 'admin@switch.test', 'Admin Pemkab Test', 'Admin_Pemkab'))
            ->get('/users/switch/' . $ids['kabid_id']);

        $sessionData = session()->get();
        $this->assertEquals('Kepala_Bidang', $sessionData['nama_role']);

        $dashRes = $this->withSession($sessionData)->get('/dashboard');
        $dashRes->assertOK();

        // Banner
        $dashRes->assertSee('Sedang Masuk sebagai Ketut Sujana (Kepala Bidang)');
        $dashRes->assertSee('Kembali ke Akun Admin Pemkab');

        // Sidebar Kepala Bidang
        $dashRes->assertSee('sidebar-menu-pimpinan');
        $dashRes->assertSee('Surat Perintah Tugas (SPT)');
        $dashRes->assertSee('Pengelolaan Arsip');
        $dashRes->assertSee('Berita Acara');

        // Menu admin tidak boleh tampil
        $dashRes->assertDontSee('Master OPD');
        $dashRes->assertDontSee('Penilaian Arsip');
        $dashRes->assertDontSee('Manajemen Akun');

        // URL langsung diblokir
        $opdDirect = $this->withSession($sessionData)->get('/opd');
        $opdDirect->assertRedirectTo('/dashboard');
    }

    public function testImpersonatePimpinanSidebarAndAccessControl(): void
    {
        $ids = $this->setupTestData();

        // Switch ke Pimpinan
        $this->withSession($this->sess($ids['admin_id'], 'admin@switch.test', 'Admin Pemkab Test', 'Admin_Pemkab'))
            ->get('/users/switch/' . $ids['pimpinan_id']);

        $sessionData = session()->get();
        $this->assertEquals('Pimpinan', $sessionData['nama_role']);

        $dashRes = $this->withSession($sessionData)->get('/dashboard');
        $dashRes->assertOK();

        // Banner
        $dashRes->assertSee('Sedang Masuk sebagai I Made Pimpinan (Kepala Dinas)');
        $dashRes->assertSee('Kembali ke Akun Admin Pemkab');

        // Sidebar Pimpinan
        $dashRes->assertSee('sidebar-menu-pimpinan');
        $dashRes->assertSee('Surat Perintah Tugas (SPT)');
        $dashRes->assertSee('Pengelolaan Arsip');
        $dashRes->assertSee('Berita Acara');

        // Menu admin tidak boleh tampil
        $dashRes->assertDontSee('Master OPD');
        $dashRes->assertDontSee('Penilaian Arsip');
        $dashRes->assertDontSee('Manajemen Akun');

        // URL langsung diblokir
        $opdDirect = $this->withSession($sessionData)->get('/opd');
        $opdDirect->assertRedirectTo('/dashboard');
    }

    public function testImpersonateAdminOpdSidebarAndAccessControl(): void
    {
        $ids = $this->setupTestData();

        // Switch ke Admin OPD
        $this->withSession($this->sess($ids['admin_id'], 'admin@switch.test', 'Admin Pemkab Test', 'Admin_Pemkab'))
            ->get('/users/switch/' . $ids['target_id']);

        $sessionData = session()->get();
        $this->assertEquals('Admin_OPD', $sessionData['nama_role']);

        $dashRes = $this->withSession($sessionData)->get('/dashboard');
        $dashRes->assertOK();

        // Banner
        $dashRes->assertSee('Sedang Masuk sebagai Admin OPD Target (Admin OPD)');
        $dashRes->assertSee('Kembali ke Akun Admin Pemkab');

        // Sidebar Admin OPD
        $dashRes->assertSee('Manajemen Akun');
        $dashRes->assertSee('Master Bidang');
        $dashRes->assertSee('Master JRA');

        // Menu khusus Admin Pemkab tidak boleh tampil
        $dashRes->assertDontSee('Master OPD');
        $dashRes->assertDontSee('Penilaian Arsip');
        $dashRes->assertDontSee('Access Log');
        $dashRes->assertDontSee('Activity Log');

        // URL langsung ke menu Admin Pemkab diblokir
        $opdDirect = $this->withSession($sessionData)->get('/opd');
        $opdDirect->assertRedirectTo('/dashboard');

        $penilaianDirect = $this->withSession($sessionData)->get('/penilaian');
        $penilaianDirect->assertRedirectTo('/dashboard');
    }

    public function testSwitchBackRestoresAdminPemkabSessionAndSidebar(): void
    {
        $ids = $this->setupTestData();

        // 1. Simulasikan sedang impersonate Arsiparis
        $this->withSession($this->sess($ids['admin_id'], 'admin@switch.test', 'Admin Pemkab Test', 'Admin_Pemkab'))
            ->get('/users/switch/' . $ids['arsiparis_id']);

        $impersonatedSession = session()->get();
        $this->assertTrue(isset($impersonatedSession['original_admin_id']));

        // 2. Klik tombol kembali ke Admin Pemkab
        $backRes = $this->withSession($impersonatedSession)->get('/users/switch-back');
        $backRes->assertRedirectTo('/dashboard');

        // 3. Verifikasi session kembali ke Admin Pemkab asli
        $adminSession = session()->get();
        $this->assertEquals('admin-pemkab-1', $adminSession['user_id']);
        $this->assertEquals('Admin_Pemkab', $adminSession['nama_role']);
        $this->assertFalse(isset($adminSession['original_admin_id']));

        // 4. Akses Dashboard sebagai Admin Pemkab asli
        $dashRes = $this->withSession($adminSession)->get('/dashboard');
        $dashRes->assertOK();

        // Banner penyamaran TIDAK boleh ada lagi
        $dashRes->assertDontSee('Sedang Masuk sebagai');
        $dashRes->assertDontSee('Kembali ke Akun Admin Pemkab');

        // Seluruh menu Admin Pemkab harus tampil lengkap kembali
        $dashRes->assertSee('Master OPD');
        $dashRes->assertSee('Master Bidang');
        $dashRes->assertSee('Master JRA');
        $dashRes->assertSee('Manajemen Akun');
        $dashRes->assertSee('Penilaian Arsip');
        $dashRes->assertSee('Access Log');
        $dashRes->assertSee('Activity Log');

        // Admin Pemkab asli bisa mengakses /opd dengan sukses (Status 200)
        $opdRes = $this->withSession($adminSession)->get('/opd');
        $opdRes->assertOK();
    }

    public function testAdminPemkabCanPerformFullCrudOnMasterJra(): void
    {
        $ids = $this->setupTestData();
        $adminSession = $this->sess($ids['admin_id'], 'admin@switch.test', 'Admin Pemkab Test', 'Admin_Pemkab');

        // 1. Read (GET /jra)
        $indexRes = $this->withSession($adminSession)->get('/jra');
        $indexRes->assertOK();
        $indexRes->assertSee('Master Jadwal Retensi Arsip (JRA)');
        $indexRes->assertSee('Tambah JRA Baru');

        // 2. Create (POST /jra/store)
        $uniqueKode = '999.8.' . time();
        $storeRes = $this->withSession($adminSession)->post('/jra/store', [
            'kode'                 => $uniqueKode,
            'nama_klasifikasi'     => 'Klasifikasi Uji Admin Pemkab',
            'deskripsi'            => 'Deskripsi awal JRA',
            'retensi_aktif'        => 3,
            'retensi_inaktif'      => 2,
            'keterangan_retensi'   => 'Permanen',
            'klasifikasi_keamanan' => 'Biasa',
            'hak_akses'            => 'Terbuka',
            'akses_publik'         => 'Umum',
            'dasar_pertimbangan'   => 'Perbup Buleleng',
            'unit_pengolah'        => 'Sekretariat',
        ]);
        $storeRes->assertRedirectTo('/jra');

        $db = \Config\Database::connect('tests');
        $klas = $db->table('kode_klasifikasi')->where('kode', $uniqueKode)->get()->getRowArray();
        $this->assertNotNull($klas);

        $jra = $db->table('jra')->where('id_kode_klasifikasi', $klas['id_kode_klasifikasi'])->get()->getRowArray();
        $this->assertNotNull($jra);
        $this->assertEquals(3, (int) $jra['retensi_aktif']);
        $this->assertEquals('Permanen', $jra['keterangan_retensi']);

        // 3. Update (POST /jra/update/{id})
        $updateRes = $this->withSession($adminSession)->post('/jra/update/' . $jra['id_jra'], [
            'kode'                 => $uniqueKode,
            'nama_klasifikasi'     => 'Klasifikasi Uji Admin Pemkab Updated',
            'deskripsi'            => 'Deskripsi diperbarui',
            'retensi_aktif'        => 5,
            'retensi_inaktif'      => 5,
            'keterangan_retensi'   => 'Dinilai Kembali',
            'klasifikasi_keamanan' => 'Terbatas',
            'hak_akses'            => 'Internal',
            'akses_publik'         => 'Terbatas',
            'dasar_pertimbangan'   => 'Perbup Baru',
            'unit_pengolah'        => 'Bidang Kearsipan',
        ]);
        $updateRes->assertRedirectTo('/jra');

        $jraUpdated = $db->table('jra')->where('id_jra', $jra['id_jra'])->get()->getRowArray();
        $this->assertEquals(5, (int) $jraUpdated['retensi_aktif']);
        $this->assertEquals('Dinilai Kembali', $jraUpdated['keterangan_retensi']);

        // 4. Delete (GET /jra/delete/{id})
        $deleteRes = $this->withSession($adminSession)->get('/jra/delete/' . $jra['id_jra']);
        $deleteRes->assertRedirectTo('/jra');

        $jraDeleted = $db->table('jra')->where('id_jra', $jra['id_jra'])->get()->getRowArray();
        $this->assertNull($jraDeleted);

        // Cleanup kode_klasifikasi
        $db->table('kode_klasifikasi')->where('id_kode_klasifikasi', $klas['id_kode_klasifikasi'])->delete();
    }
}
