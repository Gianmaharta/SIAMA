<?php

namespace Tests\App\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class PimpinanDashboardTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $DBGroup     = 'tests';
    protected $migrate     = false;
    protected $migrateOnce = false;
    protected $refresh     = false;
    protected $seed        = '';

    protected function sessPimpinan(string $nama = 'Drs. H. Pimpinan Daerah, M.Si', string $idOpd = 'opd-test-1', string $userId = 'user-pimpinan-001'): array
    {
        return [
            'user_id'             => $userId,
            'email'               => 'pimpinan@siama.test',
            'nama_lengkap'        => $nama,
            'nama_role'           => 'Pimpinan',
            'id_role'             => '2',
            'id_opd'              => $idOpd,
            'id_bidang'           => null,
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];
    }

    public function testPimpinanDashboardRendersAllFiveSectionsAccurately()
    {
        $result = $this->withSession($this->sessPimpinan('Drs. H. Pimpinan Daerah, M.Si'))
                       ->get('/dashboard');

        $result->assertOK();
        $body = $result->getBody();

        // 1. Heading & Subheading
        $result->assertSee('Selamat Datang, Drs. H. Pimpinan Daerah, M.Si!');
        $result->assertSee('Ini adalah ringkasan statistik dan monitoring alih media berdasarkan akses Anda sebagai Kepala Dinas');

        // 2. Section 1: Ringkasan Statistik (4 Kartu)
        $result->assertSee('Total SPT Diterbitkan');
        $result->assertSee('Menanti TTD Pimpinan');
        $result->assertSee('Total Arsip Dialihmediakan');
        $result->assertSee('Berita Acara Selesai');

        // 3. Section 2: Dokumen Membutuhkan Tindakan Pimpinan
        $result->assertSee('Dokumen Membutuhkan Tindakan Pimpinan');
        $result->assertSee('Berita Acara yang telah diverifikasi Kepala Bidang dan siap untuk ditandatangani atau disahkan.');

        // 4. Section 3: Monitoring Pelaksanaan SPT
        $result->assertSee('Monitoring Pelaksanaan SPT');
        $result->assertSee('Pantau progres dan tahapan pelaksanaan alih media arsip dari setiap Surat Perintah Tugas.');
        $result->assertSee('Lihat Semua SPT');
        $result->assertSee('Nomor SPT');
        $result->assertSee('Tanggal SPT');
        $result->assertSee('Perihal Penugasan');
        $result->assertSee('Status Pelaksanaan');

        // 5. Section 4: Riwayat Berita Acara & Filter Tabs
        $result->assertSee('Riwayat Berita Acara');
        $result->assertSee('Lihat Semua BA');
        $result->assertSee('Semua');
        $result->assertSee('Menunggu Tindakan');
        $result->assertSee('Selesai');
        $result->assertSee('Perbaikan / Revisi');
        $result->assertSee('Draf Kabid');

        // 6. Section Aktivitas Terbaru telah dihapus dari Dashboard Pimpinan sesuai permintaan
        $this->assertStringNotContainsString('Aktivitas Terbaru', $body);
        $this->assertStringNotContainsString('Catatan rekam jejak aktivitas pengarsipan dan operasional', $body);

        // 7. SptTerbitkanModal constraint: Dashboard must NOT have modal or create button
        $this->assertStringNotContainsString('class="spt-btn-create', $body);
        $this->assertStringNotContainsString('modalTerbitkanSpt', $body);
    }

    public function testPimpinanDashboardActionRequiredOnlyShowsDrafPimpinan()
    {
        $db = \Config\Database::connect();

        $idOpd = uniqid('opd-pimp-');
        $db->table('opd')->insert([
            'id_opd'     => $idOpd,
            'kode_opd'   => 'OPD-' . strtoupper(substr(uniqid(), 0, 5)),
            'nama_opd'   => 'Dinas Pengujian Pimpinan',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $idPimpinan = uniqid('user-pimp-');
        $db->table('users')->insert([
            'id_user'    => $idPimpinan,
            'id_opd'     => $idOpd,
            'nama'       => 'Ir. Kadis Pengujian',
            'email'      => 'kadis.' . uniqid() . '@siama.test',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $idArsiparis = uniqid('user-arsip-');
        $db->table('users')->insert([
            'id_user'    => $idArsiparis,
            'id_opd'     => $idOpd,
            'nama'       => 'Arsiparis Pelaksana',
            'email'      => 'arsip.' . uniqid() . '@siama.test',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // 1. SPT
        $idSpt = uniqid('spt-test-');
        $db->table('spt')->insert([
            'id_spt'           => $idSpt,
            'id_opd'           => $idOpd,
            'id_pimpinan'      => $idPimpinan,
            'nomor_spt'        => '090/TEST-PIMP/2026',
            'tanggal_spt'      => '2026-03-01',
            'perihal'          => 'Alih Media Arsip Dinas Pengujian',
            'status'           => 'Aktif',
            'status_penugasan' => 'ditugaskan',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        // 2. Berita Acara yang siap TTD Pimpinan (Draf_Pimpinan)
        $idBaSiap = uniqid('ba-siap-');
        $db->table('berita_acara')->insert([
            'id_berita_acara'    => $idBaSiap,
            'id_opd'             => $idOpd,
            'id_spt'             => $idSpt,
            'id_pelaksana'       => $idArsiparis,
            'nomor_ba'           => 'BA/READY/001',
            'tanggal_ba'         => '2026-03-10',
            'status_persetujuan' => 'Draf_Pimpinan',
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        // 3. Berita Acara yang masih di tahap Kabid (Draf_Kabid) -> TIDAK BOLEH muncul di Dokumen Membutuhkan Tindakan Pimpinan
        $idBaKabid = uniqid('ba-kabid-');
        $db->table('berita_acara')->insert([
            'id_berita_acara'    => $idBaKabid,
            'id_opd'             => $idOpd,
            'id_spt'             => $idSpt,
            'id_pelaksana'       => $idArsiparis,
            'nomor_ba'           => 'BA/NOT-READY/002',
            'tanggal_ba'         => '2026-03-11',
            'status_persetujuan' => 'Draf_Kabid',
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        // 4. Berita Acara Selesai
        $idBaSelesai = uniqid('ba-selesai-');
        $db->table('berita_acara')->insert([
            'id_berita_acara'    => $idBaSelesai,
            'id_opd'             => $idOpd,
            'id_spt'             => $idSpt,
            'id_pelaksana'       => $idArsiparis,
            'nomor_ba'           => 'BA/SELESAI/003',
            'tanggal_ba'         => '2026-03-12',
            'status_persetujuan' => 'Selesai',
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        // 5. Arsip
        $db->table('arsip')->insert([
            'id_arsip'           => uniqid('arsip-'),
            'id_spt'             => $idSpt,
            'id_opd'             => $idOpd,
            'nomor_arsip'        => 'ARS/001',
            'nama_arsip'         => 'Berkas Arsip Pimpinan 1',
            'status_verifikasi'  => 'Selesai',
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        $sessionData = $this->sessPimpinan('Ir. Kadis Pengujian', $idOpd, $idPimpinan);
        $result = $this->withSession($sessionData)->get('/dashboard');

        $result->assertOK();
        $body = $result->getBody();

        // Di Dokumen Membutuhkan Tindakan Pimpinan:
        // BA/READY/001 harus ada
        $result->assertSee('BA/READY/001');
        $this->assertStringContainsString('Tinjau &amp; Sahkan', $body);
        $result->assertSee('Menunggu TTD Pimpinan');

        // Pastikan BA/NOT-READY/002 TIDAK ada di dalam container Dokumen Membutuhkan Tindakan
        $actionSection = explode('<!-- 3. MONITORING PELAKSANAAN SPT -->', $body)[0];
        $this->assertStringContainsString('BA/READY/001', $actionSection);
        $this->assertStringNotContainsString('BA/NOT-READY/002', $actionSection);

        // Di Riwayat Berita Acara: semua BA dari OPD ini muncul
        $result->assertSee('BA/READY/001');
        $result->assertSee('BA/NOT-READY/002');
        $result->assertSee('BA/SELESAI/003');

        // Di Monitoring SPT: nomor SPT muncul
        $result->assertSee('090/TEST-PIMP/2026');
        $result->assertSee('Alih Media Arsip Dinas Pengujian');
    }
}
