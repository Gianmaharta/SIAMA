<?php

namespace Tests\App\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Services\NotificationService;
use App\Models\NotificationModel;

class NotificationWorkflowTest extends CIUnitTestCase
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
        $db->table('opd')->insert(['id_opd' => 'opd-1', 'nama_opd' => 'Dinas Komunikasi dan Informatika']);
        $db->table('opd')->insert(['id_opd' => 'opd-2', 'nama_opd' => 'Dinas Pendidikan']);

        // 3. Users
        $users = [
            ['id_user' => 'u-admin-pemkab', 'nama' => 'Admin Pemkab', 'email' => 'adm.pemkab@test.com', 'id_opd' => null, 'r' => 'r-admin-pemkab'],
            ['id_user' => 'u-admin-opd', 'nama' => 'Admin OPD', 'email' => 'adm.opd@test.com', 'id_opd' => 'opd-1', 'r' => 'r-admin-opd'],
            ['id_user' => 'u-pimpinan', 'nama' => 'Kepala Dinas', 'email' => 'pimpinan@test.com', 'id_opd' => 'opd-1', 'r' => 'r-pimpinan'],
            ['id_user' => 'u-kabid', 'nama' => 'Kepala Bidang TI', 'email' => 'kabid@test.com', 'id_opd' => 'opd-1', 'r' => 'r-kabid'],
            ['id_user' => 'u-arsiparis', 'nama' => 'Arsiparis Utama', 'email' => 'arsiparis@test.com', 'id_opd' => 'opd-1', 'r' => 'r-arsiparis'],
            ['id_user' => 'u-arsiparis-lain', 'nama' => 'Arsiparis Lain', 'email' => 'arsiparis2@test.com', 'id_opd' => 'opd-1', 'r' => 'r-arsiparis'],
            ['id_user' => 'u-kabid-opd2', 'nama' => 'Kabid OPD Lain', 'email' => 'kabid2@test.com', 'id_opd' => 'opd-2', 'r' => 'r-kabid'],
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
            'opd_id'         => 'opd-1',
            'pemkab_id'      => 'u-admin-pemkab',
            'admin_id'       => 'u-admin-opd',
            'pimpinan'       => 'u-pimpinan',
            'kabid'          => 'u-kabid',
            'arsiparis'      => 'u-arsiparis',
            'arsiparis_lain' => 'u-arsiparis-lain',
            'kabid_opd2'     => 'u-kabid-opd2',
        ];
    }

    public function testAllNineNotificationScenarios(): void
    {
        $ids = $this->setupUsersAndRoles();
        $db = $this->db();
        $service = new NotificationService();

        // -------------------------------------------------------------
        // Skenario A: Pimpinan menerbitkan SPT -> Kabid di OPD terkait menerima
        // -------------------------------------------------------------
        $db->table('spt')->insert([
            'id_spt' => 'spt-901',
            'id_opd' => $ids['opd_id'],
            'nomor_spt' => '001/SPT/DISKOMINFO/2026',
            'tanggal_spt' => '2026-11-17',
            'id_pimpinan' => $ids['pimpinan'],
            'perihal' => 'Alih Media Arsip Penting',
            'tanggal_mulai' => '2026-11-17',
            'tanggal_selesai' => '2026-11-20',
            'status' => 'Aktif',
            'status_penugasan' => 'belum_ditugaskan'
        ]);

        $notifIdsA = $service->notifySptCreated('spt-901', $ids['pimpinan']);
        $this->assertNotEmpty($notifIdsA, 'Skenario A harus mengirim notifikasi ke Kabid terkait');
        
        $notifA = $db->table('notifications')->where('id_notification', $notifIdsA[0])->get()->getRowArray();
        $this->assertEquals($ids['kabid'], $notifA['id_user'], 'Penerima harus Kepala Bidang di OPD terkait');
        $this->assertEquals($ids['pimpinan'], $notifA['sender_id'], 'Pengirim harus Pimpinan');
        $this->assertStringContainsString('Surat Perintah Tugas baru telah diterbitkan', $notifA['message']);
        $this->assertStringContainsString('001/SPT/DISKOMINFO/2026', $notifA['message']);

        // Pastikan Kabid di OPD lain TIDAK menerima notifikasi (Anti-bocor)
        $notifKabid2 = $db->table('notifications')->where('id_user', $ids['kabid_opd2'])->get()->getResultArray();
        $this->assertEmpty($notifKabid2, 'Kabid OPD 2 tidak boleh menerima notifikasi OPD 1');

        // -------------------------------------------------------------
        // Skenario B: Kepala Bidang menugaskan Arsiparis -> Arsiparis terpilih menerima
        // -------------------------------------------------------------
        $notifIdB = $service->notifySptAssigned('spt-901', $ids['arsiparis'], $ids['kabid']);
        $this->assertNotNull($notifIdB, 'Skenario B harus mengirim notifikasi ke Arsiparis');

        $notifB = $db->table('notifications')->where('id_notification', $notifIdB)->get()->getRowArray();
        $this->assertEquals($ids['arsiparis'], $notifB['id_user']);
        $this->assertEquals($ids['kabid'], $notifB['sender_id']);
        $this->assertStringContainsString('Anda mendapatkan penugasan alih media arsip baru', $notifB['message']);

        // Arsiparis lain tidak boleh menerima penugasan ini
        $notifArsiparisLain = $db->table('notifications')->where('id_user', $ids['arsiparis_lain'])->get()->getResultArray();
        $this->assertEmpty($notifArsiparisLain, 'Arsiparis lain tidak boleh menerima notifikasi tugas orang lain');

        // -------------------------------------------------------------
        // Skenario C: Arsiparis memulai pekerjaan -> Kabid menerima
        // -------------------------------------------------------------
        $notifIdC = $service->notifySptStarted('spt-901', $ids['arsiparis']);
        $this->assertNotNull($notifIdC, 'Skenario C harus mengirim notifikasi ke Kabid penugasan');

        $notifC = $db->table('notifications')->where('id_notification', $notifIdC)->get()->getRowArray();
        $this->assertEquals($ids['kabid'], $notifC['id_user']);
        $this->assertEquals($ids['arsiparis'], $notifC['sender_id']);
        $this->assertStringContainsString('Proses alih media untuk SPT 001/SPT/DISKOMINFO/2026 telah dimulai', $notifC['message']);

        // -------------------------------------------------------------
        // Skenario D: Arsiparis mengirim hasil alih media / BA draf -> Kabid menerima verifikasi
        // -------------------------------------------------------------
        $db->table('arsip')->insert([
            'id_arsip' => 501,
            'id_opd' => $ids['opd_id'],
            'id_spt' => 'spt-901',
            'nomor_arsip' => 'ARS/901/2026',
            'nama_arsip' => 'SK Dinas Baru',
            'status_verifikasi' => 'Menunggu',
            'id_user_upload' => $ids['arsiparis'],
            'created_by' => $ids['arsiparis']
        ]);

        $notifIdsD = $service->notifyArsipUploaded('501', $ids['arsiparis']);
        $this->assertNotEmpty($notifIdsD);
        $notifD = $db->table('notifications')->where('id_notification', $notifIdsD[0])->get()->getRowArray();
        $this->assertEquals($ids['kabid'], $notifD['id_user']);
        $this->assertStringContainsString('Hasil alih media telah dikirim dan menunggu verifikasi', $notifD['message']);

        // -------------------------------------------------------------
        // Skenario E: Kepala Bidang menyetujui Berita Acara -> Pimpinan menerima
        // -------------------------------------------------------------
        $db->table('berita_acara')->insert([
            'id_berita_acara' => 'ba-901',
            'id_opd' => $ids['opd_id'],
            'id_spt' => 'spt-901',
            'nomor_ba' => '001/BA/2026',
            'tanggal_ba' => '2026-11-17',
            'status_persetujuan' => 'Draf_Kabid',
            'id_pelaksana' => $ids['arsiparis'],
            'id_kabid' => $ids['kabid']
        ]);

        $notifIdsE = $service->notifyBaKabidVerified('ba-901', 'Approve', null, $ids['kabid']);
        $this->assertNotEmpty($notifIdsE, 'Skenario E harus mengirim notifikasi ke Pimpinan');
        $notifE = $db->table('notifications')->where('id_notification', $notifIdsE[0])->get()->getRowArray();
        $this->assertEquals($ids['pimpinan'], $notifE['id_user']);
        $this->assertEquals($ids['kabid'], $notifE['sender_id']);
        $this->assertStringContainsString('Berita Acara telah diverifikasi dan siap ditindaklanjuti oleh Pimpinan', $notifE['message']);

        // -------------------------------------------------------------
        // Skenario F: Kepala Bidang meminta revisi -> Arsiparis pelaksana menerima
        // -------------------------------------------------------------
        $notifIdsF = $service->notifyBaKabidVerified('ba-901', 'Reject', 'Format scan miring, harap scan ulang', $ids['kabid']);
        $this->assertNotEmpty($notifIdsF, 'Skenario F harus mengirim notifikasi revisi ke Arsiparis');
        $notifF = $db->table('notifications')->where('id_notification', $notifIdsF[0])->get()->getRowArray();
        $this->assertEquals($ids['arsiparis'], $notifF['id_user']);
        $this->assertEquals($ids['kabid'], $notifF['sender_id']);
        $this->assertStringContainsString('Hasil alih media memerlukan perbaikan. Silakan periksa catatan revisi', $notifF['message']);
        $this->assertStringContainsString('Format scan miring, harap scan ulang', $notifF['message']);

        // -------------------------------------------------------------
        // Skenario G: Arsiparis mengirim ulang hasil revisi -> Kabid menerima
        // -------------------------------------------------------------
        $notifIdsG = $service->notifyBaRevisionResubmitted('ba-901', $ids['arsiparis']);
        $this->assertNotEmpty($notifIdsG, 'Skenario G harus mengirim notifikasi ke Kabid');
        $notifG = $db->table('notifications')->where('id_notification', $notifIdsG[0])->get()->getRowArray();
        $this->assertEquals($ids['kabid'], $notifG['id_user']);
        $this->assertEquals($ids['arsiparis'], $notifG['sender_id']);
        $this->assertStringContainsString('Hasil perbaikan telah dikirim kembali untuk diperiksa', $notifG['message']);

        // -------------------------------------------------------------
        // Skenario H: Pimpinan menandatangani Berita Acara -> Kabid & Arsiparis menerima
        // -------------------------------------------------------------
        $notifIdsH = $service->notifyBaPimpinanSigned('ba-901', $ids['pimpinan']);
        $this->assertGreaterThanOrEqual(2, count($notifIdsH), 'Skenario H harus memberitahu Kabid dan Arsiparis');
        $recipientsH = $db->table('notifications')->whereIn('id_notification', $notifIdsH)->get()->getResultArray();
        $recipientIdsH = array_column($recipientsH, 'id_user');
        $this->assertContains($ids['kabid'], $recipientIdsH);
        $this->assertContains($ids['arsiparis'], $recipientIdsH);
        foreach ($recipientsH as $nH) {
            $this->assertStringContainsString('Berita Acara telah ditandatangani oleh Pimpinan', $nH['message']);
            $this->assertEquals($ids['pimpinan'], $nH['sender_id']);
        }

        // -------------------------------------------------------------
        // Skenario I: Pimpinan menolak / meminta perbaikan Berita Acara -> Kabid menerima
        // -------------------------------------------------------------
        $notifIdsI = $service->notifyBaPimpinanRejected('ba-901', $ids['pimpinan'], 'Tanda tangan saksi belum lengkap');
        $this->assertNotEmpty($notifIdsI, 'Skenario I harus mengirim notifikasi ke Kabid');
        $notifI = $db->table('notifications')->where('id_notification', $notifIdsI[0])->get()->getRowArray();
        $this->assertEquals($ids['kabid'], $notifI['id_user']);
        $this->assertEquals($ids['pimpinan'], $notifI['sender_id']);
        $this->assertStringContainsString('Berita Acara memerlukan tindak lanjut. Silakan periksa catatan dari Pimpinan', $notifI['message']);
        $this->assertStringContainsString('Tanda tangan saksi belum lengkap', $notifI['message']);

        // -------------------------------------------------------------
        // Anti-Duplicate Test: Mengirim notifikasi persis sama dalam 5 menit harus diabaikan
        // -------------------------------------------------------------
        $dupNotif = $service->notifyBaPimpinanRejected('ba-901', $ids['pimpinan'], 'Tanda tangan saksi belum lengkap');
        $this->assertEmpty($dupNotif, 'Panggilan kedua dalam 5 menit untuk notifikasi identik harus ditolak (anti spam/duplikat)');
    }
}
