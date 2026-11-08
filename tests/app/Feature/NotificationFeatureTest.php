<?php

namespace Tests\App\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Services\NotificationService;
use App\Models\NotificationModel;

class NotificationFeatureTest extends CIUnitTestCase
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
        foreach (['notifications', 'user_roles', 'users', 'opd', 'roles', 'spt', 'spt_assignments', 'arsip', 'berita_acara'] as $t) {
            $db->query("TRUNCATE TABLE `$t`");
        }
        $db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $db = $this->db();
        $db->query('SET FOREIGN_KEY_CHECKS=0');
        foreach (['notifications', 'user_roles', 'users', 'opd', 'roles', 'spt', 'spt_assignments', 'arsip', 'berita_acara'] as $t) {
            $db->query("TRUNCATE TABLE `$t`");
        }
        $db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function setupUsersAndRoles(): array
    {
        $db = $this->db();

        // 1. Roles
        $db->table('roles')->insert(['id_role' => 'r-admin-pemkab', 'nama_role' => 'Admin_Pemkab']);
        $db->table('roles')->insert(['id_role' => 'r-admin-opd',    'nama_role' => 'Admin_OPD']);
        $db->table('roles')->insert(['id_role' => 'r-pimpinan',     'nama_role' => 'Pimpinan']);
        $db->table('roles')->insert(['id_role' => 'r-kabid',        'nama_role' => 'Kepala_Bidang']);
        $db->table('roles')->insert(['id_role' => 'r-arsiparis',    'nama_role' => 'Arsiparis']);

        // 2. OPD
        $db->table('opd')->insert(['id_opd' => 'opd-1', 'nama_opd' => 'Dinas Kearsipan Test']);

        // 3. Users
        $users = [
            ['id_user' => 'u-admin-pemkab', 'nama' => 'Admin Pemkab', 'email' => 'adm.pemkab@test.com', 'id_opd' => null, 'r' => 'r-admin-pemkab'],
            ['id_user' => 'u-admin-opd', 'nama' => 'Admin OPD', 'email' => 'adm.opd@test.com', 'id_opd' => 'opd-1', 'r' => 'r-admin-opd'],
            ['id_user' => 'u-pimpinan', 'nama' => 'Kepala Dinas', 'email' => 'pimpinan@test.com', 'id_opd' => 'opd-1', 'r' => 'r-pimpinan'],
            ['id_user' => 'u-kabid', 'nama' => 'Kabid Kearsipan', 'email' => 'kabid@test.com', 'id_opd' => 'opd-1', 'r' => 'r-kabid'],
            ['id_user' => 'u-arsiparis', 'nama' => 'Arsiparis Test', 'email' => 'arsiparis@test.com', 'id_opd' => 'opd-1', 'r' => 'r-arsiparis'],
        ];

        foreach ($users as $u) {
            $db->table('users')->insert([
                'id_user' => $u['id_user'],
                'nama' => $u['nama'],
                'email' => $u['email'],
                'password' => password_hash('Pass123!', PASSWORD_DEFAULT),
                'id_opd' => $u['id_opd'],
                'is_active' => 1,
                'is_default_password' => 0
            ]);
            $db->table('user_roles')->insert([
                'id_user' => $u['id_user'],
                'id_role' => $u['r']
            ]);
        }

        return [
            'opd_id'    => 'opd-1',
            'pemkab_id' => 'u-admin-pemkab',
            'admin_id'  => 'u-admin-opd',
            'pimpinan'  => 'u-pimpinan',
            'kabid'     => 'u-kabid',
            'arsiparis' => 'u-arsiparis',
        ];
    }

    public function testNotificationServiceScenarios(): void
    {
        $ids = $this->setupUsersAndRoles();
        $db = $this->db();
        $service = new NotificationService();

        // 1. Skenario A: Penugasan SPT ke Arsiparis
        $db->table('spt')->insert([
            'id_spt' => 'spt-101',
            'id_opd' => $ids['opd_id'],
            'nomor_spt' => '001/SPT/TEST/2026',
            'tanggal_spt' => '2026-11-01',
            'id_pimpinan' => $ids['pimpinan'],
            'perihal' => 'Alih Media Arsip Kepegawaian',
            'tanggal_mulai' => '2026-11-01',
            'tanggal_selesai' => '2026-11-05',
            'status' => 'Aktif',
            'status_penugasan' => 'ditugaskan'
        ]);

        $notifIdA = $service->notifySptAssigned('spt-101', $ids['arsiparis'], $ids['kabid']);
        $this->assertNotNull($notifIdA);

        $notifA = $db->table('notifications')->where('id_notification', $notifIdA)->get()->getRowArray();
        $this->assertEquals($ids['arsiparis'], $notifA['id_user']);
        $this->assertStringContainsString('Penugasan Alih Media Baru', $notifA['title']);
        $this->assertStringContainsString('001/SPT/TEST/2026', $notifA['message']);
        $this->assertEquals('spt', $notifA['type']);

        // 2. Skenario B: Arsiparis Upload Arsip -> Notifikasi ke Kabid
        $db->table('spt_assignments')->insert([
            'id_assignment' => 'assign-101',
            'id_spt' => 'spt-101',
            'id_user' => $ids['arsiparis'],
            'assigned_by' => $ids['kabid'],
            'status' => 'proses'
        ]);

        $db->table('arsip')->insert([
            'id_arsip' => 201,
            'id_opd' => $ids['opd_id'],
            'id_spt' => 'spt-101',
            'nomor_arsip' => 'ARS/001/2026',
            'nama_arsip' => 'Berkas SK Pengangkatan',
            'status_verifikasi' => 'Menunggu',
            'id_user_upload' => $ids['arsiparis'],
            'created_by' => $ids['arsiparis']
        ]);

        $notifIdsB = $service->notifyArsipUploaded('201', $ids['arsiparis']);
        $this->assertNotEmpty($notifIdsB);

        $notifB = $db->table('notifications')->where('id_notification', $notifIdsB[0])->get()->getRowArray();
        $this->assertEquals($ids['kabid'], $notifB['id_user']);
        $this->assertStringContainsString('Menunggu Verifikasi', $notifB['title']);

        // 3. Skenario C1: Kabid Verifikasi Arsip -> Notifikasi ke Arsiparis
        $notifIdC1 = $service->notifyArsipVerified('201', $ids['kabid']);
        $this->assertNotNull($notifIdC1);

        $notifC1 = $db->table('notifications')->where('id_notification', $notifIdC1)->get()->getRowArray();
        $this->assertEquals($ids['arsiparis'], $notifC1['id_user']);
        $this->assertStringContainsString('Verifikasi Alih Media Disetujui', $notifC1['title']);

        // 4. Skenario C2 & D: Verifikasi Berita Acara oleh Kabid
        $db->table('berita_acara')->insert([
            'id_berita_acara' => 'ba-301',
            'id_opd' => $ids['opd_id'],
            'id_spt' => 'spt-101',
            'nomor_ba' => '001/BA-TEST/2026',
            'tanggal_ba' => '2026-11-03',
            'status_persetujuan' => 'Draf_Kabid',
            'id_pelaksana' => $ids['arsiparis'],
            'id_kabid' => $ids['kabid']
        ]);

        // D: Approve -> Kirim ke Pimpinan
        $notifIdsD = $service->notifyBaKabidVerified('ba-301', 'Approve');
        $this->assertNotEmpty($notifIdsD);
        $notifD = $db->table('notifications')->where('id_notification', $notifIdsD[0])->get()->getRowArray();
        $this->assertEquals($ids['pimpinan'], $notifD['id_user']);
        $this->assertStringContainsString('Menunggu Pengesahan', $notifD['title']);

        // C2: Reject/Revisi -> Kirim ke Arsiparis
        $notifIdsC2 = $service->notifyBaKabidVerified('ba-301', 'Reject', 'Perbaiki nomor lampiran');
        $this->assertNotEmpty($notifIdsC2);
        $notifC2 = $db->table('notifications')->where('id_notification', $notifIdsC2[0])->get()->getRowArray();
        $this->assertEquals($ids['arsiparis'], $notifC2['id_user']);
        $this->assertStringContainsString('Revisi Berita Acara', $notifC2['title']);
        $this->assertStringContainsString('Perbaiki nomor lampiran', $notifC2['message']);

        // 5. Skenario E: Pimpinan Tandatangani Berita Acara -> Kirim ke Kabid, Arsiparis, Admin OPD
        $notifIdsE = $service->notifyBaPimpinanSigned('ba-301', $ids['pimpinan']);
        $this->assertGreaterThanOrEqual(2, count($notifIdsE));

        // 6. Skenario F: Pengelolaan Akun Pengguna -> Kirim ke Admin OPD
        $notifIdsF = $service->notifyUserManagement($ids['arsiparis'], 'create', $ids['pemkab_id']);
        $this->assertNotEmpty($notifIdsF);
        $notifF = $db->table('notifications')->where('id_notification', $notifIdsF[0])->get()->getRowArray();
        $this->assertEquals($ids['admin_id'], $notifF['id_user']);
        $this->assertStringContainsString('Pengelolaan Akun', $notifF['title']);
    }

    public function testNotificationHttpEndpoints(): void
    {
        $ids = $this->setupUsersAndRoles();
        $db = $this->db();

        // Masukkan sample notifikasi untuk arsiparis
        $db->table('notifications')->insert([
            'id_notification' => 'notif-1',
            'id_user'         => $ids['arsiparis'],
            'title'           => 'Tugas Baru',
            'message'         => 'Silakan periksa berkas arsip Anda.',
            'type'            => 'spt',
            'url'             => 'http://localhost:8080/spt/detail/spt-101',
            'is_read'         => 0,
            'created_at'      => date('Y-m-d H:i:s')
        ]);
        $db->table('notifications')->insert([
            'id_notification' => 'notif-2',
            'id_user'         => $ids['arsiparis'],
            'title'           => 'Tugas Selesai',
            'message'         => 'Verifikasi telah selesai.',
            'type'            => 'arsip',
            'url'             => 'http://localhost:8080/arsip/detail/201',
            'is_read'         => 1,
            'created_at'      => date('Y-m-d H:i:s', strtotime('-1 hour'))
        ]);

        $sessionArsiparis = $this->sess($ids['arsiparis'], 'arsiparis@test.com', 'Arsiparis Test', 'Arsiparis', $ids['opd_id']);

        // 1. GET /notification
        $result = $this->withSession($sessionArsiparis)->get('/notification');
        $result->assertOK();
        $result->assertSee('Pusat Notifikasi');
        $result->assertSee('Tugas Baru');
        $result->assertSee('Tugas Selesai');

        // 2. GET /notification?filter=unread
        $resultUnread = $this->withSession($sessionArsiparis)->get('/notification?filter=unread');
        $resultUnread->assertOK();
        $resultUnread->assertSee('Tugas Baru');

        // 3. GET /notification/unread-count
        $resultCount = $this->withSession($sessionArsiparis)->get('/notification/unread-count');
        $resultCount->assertOK();
        $jsonCount = json_decode($resultCount->getJSON(), true);
        $this->assertEquals(1, $jsonCount['unread']);

        // 4. GET /notification/dropdown
        $resultDropdown = $this->withSession($sessionArsiparis)->get('/notification/dropdown');
        $resultDropdown->assertOK();
        $jsonDropdown = json_decode($resultDropdown->getJSON(), true);
        $this->assertEquals('success', $jsonDropdown['status']);
        $this->assertCount(2, $jsonDropdown['notifications']);

        // 5. POST /notification/mark-read/notif-1
        $resultMark = $this->withSession($sessionArsiparis)->post('/notification/mark-read/notif-1');
        $resultMark->assertRedirect();
        $notif1 = $db->table('notifications')->where('id_notification', 'notif-1')->get()->getRowArray();
        $this->assertEquals(1, (int)$notif1['is_read']);

        // 6. POST /notification/mark-all-read
        $resultMarkAll = $this->withSession($sessionArsiparis)->post('/notification/mark-all-read');
        $resultMarkAll->assertRedirect();

        // 7. GET /notification/read-and-redirect/notif-1
        $resultRedirect = $this->withSession($sessionArsiparis)->get('/notification/read-and-redirect/notif-1');
        $resultRedirect->assertRedirect();
    }
}

