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

    protected function setupTestData(): array
    {
        $db = $this->db();

        // Buat role
        $db->table('roles')->insert(['id_role' => 'r-admin', 'nama_role' => 'Admin_Pemkab']);
        $db->table('roles')->insert(['id_role' => 'r-opd',   'nama_role' => 'Admin_OPD']);

        // Buat OPD
        $db->table('opd')->insert(['id_opd' => 'opd-test', 'nama_opd' => 'OPD Test Switch']);

        // Buat user Admin Pemkab
        $db->table('users')->insert([
            'id_user' => 'admin-pemkab-1', 'nama' => 'Admin Pemkab Test',
            'email' => 'admin@switch.test', 'password' => password_hash('Test123!', PASSWORD_DEFAULT),
            'id_opd' => null, 'is_active' => 1, 'is_default_password' => 0,
        ]);
        $db->table('user_roles')->insert(['id_user' => 'admin-pemkab-1', 'id_role' => 'r-admin']);

        // Buat user Admin OPD (target switch)
        $db->table('users')->insert([
            'id_user' => 'admin-opd-1', 'nama' => 'Admin OPD Target',
            'email' => 'opd@switch.test', 'password' => password_hash('Test123!', PASSWORD_DEFAULT),
            'id_opd' => 'opd-test', 'is_active' => 1, 'is_default_password' => 0,
        ]);
        $db->table('user_roles')->insert(['id_user' => 'admin-opd-1', 'id_role' => 'r-opd']);

        return [
            'admin_id' => 'admin-pemkab-1',
            'target_id' => 'admin-opd-1',
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
        $result->assertRedirectTo(base_url('/dashboard'));

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
        $result->assertRedirectTo(base_url('/dashboard'));

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
        $result->assertRedirectTo(base_url('/dashboard'));
    }
}
