<?php

namespace Tests\App\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class ArsiparisDashboardTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $DBGroup     = 'tests';
    protected $migrate     = false;
    protected $migrateOnce = false;
    protected $refresh     = false;
    protected $seed        = '';

    protected function sessArsiparis(string $idUser, string $nama, string $idOpd = 'opd-test'): array
    {
        return [
            'user_id'             => $idUser,
            'email'               => 'arsiparis.' . uniqid() . '@siama.test',
            'nama_lengkap'        => $nama,
            'nama_role'           => 'Arsiparis',
            'id_role'             => '5',
            'id_opd'              => $idOpd,
            'id_bidang'           => 'bidang-test',
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];
    }

    public function testArsiparisDashboardRendersAccurately()
    {
        $db = \Config\Database::connect();

        // 1. Siapkan data master OPD
        $idOpd = uniqid('opd-arsip-');
        $db->table('opd')->insert([
            'id_opd'   => $idOpd,
            'nama_opd' => 'Dinas Komunikasi dan Informatika Test',
        ]);

        // 2. Siapkan User Arsiparis & User Assigner (Kepala Bidang)
        $idUserArsiparis = uniqid('user-arsip-');
        $idUserAssigner  = uniqid('user-kabid-');

        $db->table('users')->insert([
            'id_user'    => $idUserAssigner,
            'id_opd'     => $idOpd,
            'nama'       => 'Ketut Kabid',
            'email'      => 'kabid.' . uniqid() . '@siama.test',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $db->table('users')->insert([
            'id_user'    => $idUserArsiparis,
            'id_opd'     => $idOpd,
            'nama'       => 'Budi Santoso',
            'email'      => 'budi.' . uniqid() . '@siama.test',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // 3. SPT belum dikerjakan untuk Arsiparis Budi Santoso
        $idSptTest = uniqid('spt-test-');
        $nomorSptTest = '090/TEST/' . rand(100, 999);

        $db->table('spt')->insert([
            'id_spt'           => $idSptTest,
            'id_opd'           => $idOpd,
            'nomor_spt'        => $nomorSptTest,
            'tanggal_spt'      => date('Y-m-d'),
            'perihal'          => 'Tugas Alih Media Arsip Kepegawaian Test',
            'status'           => 'Aktif',
            'status_penugasan' => 'ditugaskan',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        $db->table('spt_assignments')->insert([
            'id_assignment' => uniqid('assign-test-'),
            'id_spt'        => $idSptTest,
            'id_user'       => $idUserArsiparis,
            'assigned_by'   => $idUserAssigner,
            'status'        => 'pending',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        $result = $this->withSession($this->sessArsiparis($idUserArsiparis, 'Budi Santoso', $idOpd))
                       ->get('/dashboard');

        $result->assertOK();
        $body = $result->getBody();

        // 1. Heading & Sambutan Dashboard
        $result->assertSee('Selamat Datang, Budi Santoso!');
        $result->assertSee('Ini adalah ringkasan pekerjaan, penugasan SPT, dan perkembangan proses alih media arsip Anda');

        // 2. Prioritas Utama: Pekerjaan Belum Dikerjakan (Posisi Paling Atas)
        $result->assertSee('Pekerjaan Belum Dikerjakan');
        $result->assertSee('Daftar Surat Perintah Tugas (SPT) aktif yang ditugaskan kepada Anda');
        $result->assertSee('Nomor SPT');
        $result->assertSee('Tanggal SPT');
        $result->assertSee('Perihal / Uraian Tugas');
        $result->assertSee('Tanggal Penugasan');
        $result->assertSee('Jumlah Arsip');
        $result->assertSee('Status Pekerjaan');
        $result->assertSee('Belum Dimulai');
        $result->assertSee('Mulai Alih Media');
        $result->assertSee($nomorSptTest);
        $this->assertStringContainsString('/arsip/create?id_spt=' . $idSptTest, $body);
        $this->assertStringContainsString('/spt/detail/' . $idSptTest, $body);

        // 3. Ringkasan Pekerjaan (4 Kartu Statistik)
        $result->assertSee('Total Penugasan SPT');
        $result->assertSee('Pekerjaan Belum Dimulai');
        $result->assertSee('Arsip Sedang Diproses');
        $result->assertSee('Arsip Menunggu Verifikasi');

        // 4. Monitoring Proses Alih Media
        $result->assertSee('Monitoring Proses Alih Media');
        $result->assertSee('Nomor Arsip');
        $result->assertSee('Nama / Judul Arsip');
        $result->assertSee('Nomor SPT Terkait');
        $result->assertSee('Tahapan Proses Saat Ini');
        $result->assertSee('Pembaruan Terakhir');

        // 5. Status Verifikasi Arsip (Donut Chart & Legend)
        $result->assertSee('Status Verifikasi Arsip');
        $result->assertSee('arsiparisDonutChart');
        $result->assertSee('Total Arsip');
        $result->assertSee('Selesai Diverifikasi');
        $result->assertSee('Dalam Proses / Telaah');
        $result->assertSee('Ditolak / Revisi');

        // 6. Berita Acara
        $result->assertSee('Berita Acara');
        $result->assertSee('Nomor BA');

        // 7. Riwayat Aktivitas HANYA ada di Dashboard Admin Pemkab (tidak ada di Arsiparis)
        $this->assertStringNotContainsString('Riwayat Aktivitas', $body);

        // 8. Sidebar 3 Clean Menus & Hak Akses
        $result->assertSee('Surat Perintah Tugas (SPT)');
        $result->assertSee('Pengelolaan Arsip');
        $result->assertSee('Berita Acara');

        // Menu admin tidak boleh tampil untuk Arsiparis
        $this->assertStringNotContainsString('Manajemen Akun', $body);
        $this->assertStringNotContainsString('Master OPD', $body);
        $this->assertStringNotContainsString('Master Bidang', $body);
        $this->assertStringNotContainsString('Penilaian Arsip', $body);
        $this->assertStringNotContainsString('Master JRA', $body);
        $this->assertStringNotContainsString('Access Log', $body);
        $this->assertStringNotContainsString('Activity Log', $body);
    }

    public function testArsiparisDashboardWithNoDataShowsCleanEmptyState()
    {
        // Pengguna baru tanpa arsip / tugas / berita acara
        $sessionData = $this->sessArsiparis('user-arsiparis-empty-' . uniqid(), 'Ni Luh Ayu');

        $result = $this->withSession($sessionData)->get('/dashboard');
        $result->assertOK();

        $result->assertSee('Selamat Datang, Ni Luh Ayu!');
        $result->assertSee('Pekerjaan Belum Dikerjakan');
        $result->assertSee('Tidak ada pekerjaan tertunda');
        $result->assertSee('Semua penugasan SPT yang diterbitkan untuk Anda telah dimulai atau diselesaikan.');
        $result->assertSee('Monitoring Proses Alih Media');
        $result->assertSee('Belum ada pekerjaan alih media');
        $result->assertSee('Tidak ada data');
        $result->assertSee('Belum ada berita acara yang berkaitan dengan pekerjaan Anda.');
        $this->assertStringNotContainsString('Riwayat Aktivitas', $result->getBody());
    }

    public function testArsiparisDashboardDynamicWorkflowsAndStrictDataIsolation()
    {
        $db = \Config\Database::connect();

        $idOpdA = uniqid('opd-a-');
        $idOpdB = uniqid('opd-b-');
        $db->table('opd')->insert(['id_opd' => $idOpdA, 'nama_opd' => 'Dinas Kesehatan']);
        $db->table('opd')->insert(['id_opd' => $idOpdB, 'nama_opd' => 'Dinas Pendidikan']);

        $idAssigner = uniqid('user-assigner-');
        $db->table('users')->insert([
            'id_user'    => $idAssigner,
            'id_opd'     => $idOpdA,
            'nama'       => 'Kabid Penugasan',
            'email'      => 'assigner.' . uniqid() . '@siama.test',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $idArsiparisA = uniqid('arsiparis-a-');
        $idArsiparisB = uniqid('arsiparis-b-');

        $db->table('users')->insert([
            'id_user'    => $idArsiparisA,
            'id_opd'     => $idOpdA,
            'nama'       => 'Arsiparis Dinkes',
            'email'      => 'arsip.dinkes.' . uniqid() . '@siama.test',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $db->table('users')->insert([
            'id_user'    => $idArsiparisB,
            'id_opd'     => $idOpdB,
            'nama'       => 'Arsiparis Disdik',
            'email'      => 'arsip.disdik.' . uniqid() . '@siama.test',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $idSptA = uniqid('spt-a-');
        $idSptB = uniqid('spt-b-');

        $nomorSptA = 'SPT/A/' . rand(100, 999);
        $nomorSptB = 'SPT/B/' . rand(100, 999);

        // SPT & Assignment untuk Arsiparis A
        $db->table('spt')->insert([
            'id_spt'           => $idSptA,
            'id_opd'           => $idOpdA,
            'nomor_spt'        => $nomorSptA,
            'tanggal_spt'      => date('Y-m-d'),
            'perihal'          => 'Alih Media Arsip Kepegawaian A',
            'status'           => 'Aktif',
            'status_penugasan' => 'ditugaskan',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        $db->table('spt_assignments')->insert([
            'id_assignment' => uniqid('assign-a-'),
            'id_spt'        => $idSptA,
            'id_user'       => $idArsiparisA,
            'assigned_by'   => $idAssigner,
            'status'        => 'pending',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        // SPT & Assignment untuk Arsiparis B
        $db->table('spt')->insert([
            'id_spt'           => $idSptB,
            'id_opd'           => $idOpdB,
            'nomor_spt'        => $nomorSptB,
            'tanggal_spt'      => date('Y-m-d'),
            'perihal'          => 'Alih Media Keuangan B',
            'status'           => 'Aktif',
            'status_penugasan' => 'ditugaskan',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        $db->table('spt_assignments')->insert([
            'id_assignment' => uniqid('assign-b-'),
            'id_spt'        => $idSptB,
            'id_user'       => $idArsiparisB,
            'assigned_by'   => $idAssigner,
            'status'        => 'pending',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        $sessA = $this->sessArsiparis($idArsiparisA, 'Arsiparis Dinkes', $idOpdA);
        $sessB = $this->sessArsiparis($idArsiparisB, 'Arsiparis Disdik', $idOpdB);

        // Test Dashboard Arsiparis A
        $resA = $this->withSession($sessA)->get('/dashboard');
        $resA->assertOK();
        $bodyA = $resA->getBody();

        // Arsiparis A hanya boleh melihat SPT A, BUKAN SPT B
        $resA->assertSee($nomorSptA);
        $this->assertStringNotContainsString($nomorSptB, $bodyA);

        // Test Dashboard Arsiparis B
        $resB = $this->withSession($sessB)->get('/dashboard');
        $resB->assertOK();
        $bodyB = $resB->getBody();

        // Arsiparis B hanya boleh melihat SPT B, BUKAN SPT A
        $resB->assertSee($nomorSptB);
        $this->assertStringNotContainsString($nomorSptA, $bodyB);

        // Sekarang Arsiparis A mulai mengerjakan alih media (insert arsip di bawah SPT A)
        $idArsipA = uniqid('arsip-test-a-');
        $db->table('arsip')->insert([
            'id_arsip'          => $idArsipA,
            'id_spt'            => $idSptA,
            'id_opd'            => $idOpdA,
            'nomor_arsip'       => 'ARS/A/001',
            'nama_arsip'        => 'Berkas Arsip Digital A',
            'status_verifikasi' => 'Proses',
            'created_by'        => $idArsiparisA,
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        // Dashboard A di-refresh
        $resA2 = $this->withSession($sessA)->get('/dashboard');
        $resA2->assertOK();

        // Karena SPT A sekarang sudah ada 1 arsip, maka tugas ini berpindah dari "Belum Dikerjakan" ke "Monitoring Proses Alih Media"
        $resA2->assertSee('Berkas Arsip Digital A');
        $resA2->assertSee('Alih Media');
    }
}
