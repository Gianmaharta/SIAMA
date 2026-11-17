<?php

namespace Tests\App\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class KabidDashboardTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $DBGroup     = 'tests';
    protected $migrate     = false;
    protected $migrateOnce = false;
    protected $refresh     = false;
    protected $seed        = '';

    protected function sessKabid(string $nama = 'Budi Santoso'): array
    {
        return [
            'user_id'             => 'user-kabid-001',
            'email'               => 'kabid@siama.test',
            'nama_lengkap'        => $nama,
            'nama_role'           => 'Kepala_Bidang',
            'id_role'             => '4',
            'id_opd'              => 'opd-test-1',
            'id_bidang'           => 'bidang-test-1',
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];
    }

    public function testKabidDashboardRendersAccurately()
    {
        $result = $this->withSession($this->sessKabid('Budi Santoso'))
                       ->get('/dashboard');

        $result->assertOK();
        $body = $result->getBody();

        // 1. Heading & Subheading
        $result->assertSee('Selamat Datang, Budi Santoso!');
        $result->assertSee('Ini adalah ringkasan statistik berdasarkan akses Anda sebagai Kepala Bidang');

        // 2. Summary Cards
        $result->assertSee('Total Arsip di Bidang Anda');
        $result->assertSee('Berita Acara Menanti Verifikasi');

        // 3. Content Section (Charts)
        $result->assertSee('Status Arsip di Bidang Anda');
        $result->assertSee('kabidDonutChart');
        $result->assertSee('Arsip per Bulan (2026)');
        $result->assertSee('kabidBarChart');

        // 4. Donut Legend items
        $result->assertSee('Selesai');
        $result->assertSee('Dalam Proses');
        $result->assertSee('Belum Diproses');
        $result->assertSee('Gagal / Ditolak');

        // 5. Berita Acara Section
        $result->assertSee('Berita Acara Menanti Verifikasi Kabid');
        $result->assertSee('Tidak ada data');
        $result->assertSee('Saat ini belum ada berita acara yang menunggu verifikasi Anda.');

        // 6. Sidebar 4 Clean Menus
        $result->assertSee('Surat Perintah Tugas (SPT)');
        $result->assertSee('Pengelolaan Arsip');
        $result->assertSee('Berita Acara');

        // 7. Admin menus must NOT be present
        $this->assertStringNotContainsString('Manajemen Akun', $body);
        $this->assertStringNotContainsString('Master OPD', $body);
        $this->assertStringNotContainsString('Master JRA', $body);
        $this->assertStringNotContainsString('Access Log', $body);
        $this->assertStringNotContainsString('Activity Log', $body);
    }

    public function testKabidDashboardLoadsWithRealOpdAndBidang()
    {
        $sessionData = [
            'user_id'             => 'ec666f93-376d-4616-9d3e-62317849764e',
            'email'               => 'kabid@siama.test',
            'nama_lengkap'        => 'Kepala Bidang Kominfo',
            'nama_role'           => 'Kepala_Bidang',
            'id_role'             => '42d1c643-9b91-4997-851d-0c1309897b4e',
            'id_opd'              => '9974e50e-9512-4044-999d-7eb4061cd6b2',
            'id_bidang'           => 'fb5bbc04-7b27-43a6-8010-9b59d64e0cca',
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];

        $result = $this->withSession($sessionData)->get('/dashboard');
        $result->assertOK();

        $result->assertSee('Selamat Datang, Kepala Bidang Kominfo!');
        $result->assertSee('Status Arsip di Bidang Anda');
        $result->assertSee('Arsip per Bulan (2026)');
    }

    public function testKabidChartDataEndpointReturnsJson()
    {
        $sessionData = [
            'user_id'             => 'ec666f93-376d-4616-9d3e-62317849764e',
            'email'               => 'kabid@siama.test',
            'nama_lengkap'        => 'Kepala Bidang Kominfo',
            'nama_role'           => 'Kepala_Bidang',
            'id_role'             => '42d1c643-9b91-4997-851d-0c1309897b4e',
            'id_opd'              => '9974e50e-9512-4044-999d-7eb4061cd6b2',
            'id_bidang'           => 'fb5bbc04-7b27-43a6-8010-9b59d64e0cca',
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];

        $result = $this->withSession($sessionData)->get('/dashboard/kabid-chart-data?year=2026');
        $result->assertOK();

        $json = json_decode($result->getJSON(), true);
        $this->assertIsArray($json);
        $this->assertSame('success', $json['status'] ?? null);
        $this->assertSame('2026', $json['year'] ?? null);
        $this->assertCount(12, $json['values'] ?? []);
        $this->assertCount(12, $json['labels'] ?? []);
    }

    public function testKabidDashboardMonitoringPenugasanAndPrioritasTindakan()
    {
        $db = \Config\Database::connect();
        
        // Ambil atau buat data OPD dan Bidang pengujian
        $opd = $db->table('opd')->get(1)->getRowArray();
        $idOpd = $opd['id_opd'] ?? 'opd-test-' . time();
        if (empty($opd['id_opd'])) {
            $db->table('opd')->insert([
                'id_opd'     => $idOpd,
                'kode_opd'   => 'OPD-TEST',
                'nama_opd'   => 'Dinas Pengujian Kabid',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $idBidang = uniqid('bidang-test-');
        $db->table('bidang')->insert([
            'id_bidang'   => $idBidang,
            'id_opd'      => $idOpd,
            'nama_bidang' => 'Bidang Kearsipan Dinamis',
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        // Buat user Kabid
        $idKabid = 'user-kabid-test-' . time();
        $db->table('users')->insert([
            'id_user'    => $idKabid,
            'id_opd'     => $idOpd,
            'id_bidang'  => $idBidang,
            'nama'       => 'Drs. H. Ahmad Kabid, M.Si',
            'email'      => 'ahmad.kabid.' . time() . '@siama.test',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Buat user Arsiparis
        $idArsiparis = 'user-arsiparis-test-' . time();
        $db->table('users')->insert([
            'id_user'    => $idArsiparis,
            'id_opd'     => $idOpd,
            'id_bidang'  => $idBidang,
            'nama'       => 'Siti Arsiparis',
            'email'      => 'siti.arsip.' . time() . '@siama.test',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Buat SPT yang ditugaskan ke Arsiparis
        $idSpt = 'spt-kabid-test-' . time();
        $nomorSpt = '090/KABID/' . time();
        $db->table('spt')->insert([
            'id_spt'           => $idSpt,
            'id_opd'           => $idOpd,
            'nomor_spt'        => $nomorSpt,
            'tanggal_spt'      => date('Y-m-d'),
            'perihal'          => 'Tugas Alih Media Arsip Kabid',
            'tanggal_mulai'    => date('Y-m-d'),
            'tanggal_selesai'  => date('Y-m-d', strtotime('+7 days')),
            'status'           => 'Aktif',
            'status_penugasan' => 'ditugaskan',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        $db->table('spt_assignments')->insert([
            'id_assignment' => 'assign-kabid-' . time(),
            'id_spt'        => $idSpt,
            'id_user'       => $idArsiparis,
            'assigned_by'   => $idKabid,
            'status'        => 'assigned',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        // Buat Berita Acara Draf_Kabid
        $idBa = 'ba-kabid-test-' . time();
        $nomorBa = 'BA/KABID/' . time();
        $db->table('berita_acara')->insert([
            'id_berita_acara'    => $idBa,
            'id_spt'             => $idSpt,
            'id_opd'             => $idOpd,
            'id_pelaksana'       => $idArsiparis,
            'id_kabid'           => $idKabid,
            'nomor_ba'           => $nomorBa,
            'tanggal_ba'         => date('Y-m-d'),
            'status_persetujuan' => 'Draf_Kabid',
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        $sessionData = [
            'user_id'             => $idKabid,
            'email'               => 'ahmad.kabid@siama.test',
            'nama_lengkap'        => 'Drs. H. Ahmad Kabid, M.Si',
            'nama_role'           => 'Kepala_Bidang',
            'id_role'             => '4',
            'id_opd'              => $idOpd,
            'id_bidang'           => $idBidang,
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];

        $result = $this->withSession($sessionData)->get('/dashboard');
        $result->assertOK();
        $body = $result->getBody();

        // 1. Verifikasi Ringkasan Statistik & Prioritas Tindakan (Urutan 1)
        $result->assertSee('Total Arsip di Bidang Anda');
        $result->assertSee('Berita Acara Menanti Verifikasi');
        $result->assertSee('Pekerjaan yang Membutuhkan Tindakan Anda');
        $result->assertSee('SPT Belum Ditugaskan');
        $result->assertSee('Arsip Menunggu Verifikasi');
        $result->assertSee('BA Menunggu Pemeriksaan');
        $result->assertSee('Dokumen Perlu Perbaikan');

        // 2. Verifikasi Grafik Status Arsip & Arsip per Bulan (Urutan 2)
        $result->assertSee('Status Arsip di Bidang Anda');
        $result->assertSee('Arsip per Bulan');

        // 3. Verifikasi Bagian Monitoring Penugasan dan Proses Alih Media (Urutan 3)
        $result->assertSee('Monitoring Penugasan dan Proses Alih Media');
        $result->assertSee('Nama Arsiparis yang Ditugaskan');
        $result->assertSee('Siti Arsiparis');
        $result->assertSee($nomorSpt);
        $result->assertSee('Tahapan Alur Saat Ini');
        $result->assertSee('Status Pekerjaan');

        // 4. Verifikasi Bagian Berita Acara Menanti Verifikasi Kabid (Urutan 4)
        $result->assertSee('Berita Acara Menanti Verifikasi Kabid');
        $result->assertSee($nomorBa);

        // 5. Verifikasi Urutan Layout Sesuai Poin 5:
        // 1. Ringkasan statistik & prioritas tindakan
        // 2. Grafik dua kolom
        // 3. Monitoring Penugasan dan Proses Alih Media
        // 4. Berita Acara Menanti Verifikasi Kabid
        $posAction = strpos($body, 'Pekerjaan yang Membutuhkan Tindakan Anda');
        $posCharts = strpos($body, 'Status Arsip di Bidang Anda');
        $posMonitoring = strpos($body, 'Monitoring Penugasan dan Proses Alih Media');
        $posBa = strpos($body, 'Berita Acara Menanti Verifikasi Kabid');

        $this->assertNotFalse($posAction, 'Bagian Prioritas Tindakan tidak ditemukan');
        $this->assertNotFalse($posCharts, 'Bagian Grafik tidak ditemukan');
        $this->assertNotFalse($posMonitoring, 'Bagian Monitoring Penugasan tidak ditemukan');
        $this->assertNotFalse($posBa, 'Bagian Berita Acara tidak ditemukan');

        $this->assertLessThan($posCharts, $posAction, 'Ringkasan statistik harus sebelum grafik');
        $this->assertLessThan($posMonitoring, $posCharts, 'Grafik harus sebelum Monitoring Penugasan');
        $this->assertLessThan($posBa, $posMonitoring, 'Monitoring Penugasan harus sebelum Berita Acara');
    }

    public function testKabidDashboardStrictOpdDataIsolationAndDynamicUpdates()
    {
        $db = \Config\Database::connect();

        // 1. Buat OPD A & OPD B
        $idOpdA = uniqid('opd-a-');
        $db->table('opd')->insert([
            'id_opd'     => $idOpdA,
            'kode_opd'   => 'OPD-A-' . substr(uniqid(), 0, 4),
            'nama_opd'   => 'Dinas Kesehatan Daerah',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $idOpdB = uniqid('opd-b-');
        $db->table('opd')->insert([
            'id_opd'     => $idOpdB,
            'kode_opd'   => 'OPD-B-' . substr(uniqid(), 0, 4),
            'nama_opd'   => 'Dinas Pendidikan Daerah',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // 2. Buat Bidang di masing-masing OPD
        $idBidangA = uniqid('bidang-a-');
        $db->table('bidang')->insert([
            'id_bidang'   => $idBidangA,
            'id_opd'      => $idOpdA,
            'nama_bidang' => 'Bidang Pelayanan Kesehatan',
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        $idBidangB = uniqid('bidang-b-');
        $db->table('bidang')->insert([
            'id_bidang'   => $idBidangB,
            'id_opd'      => $idOpdB,
            'nama_bidang' => 'Bidang Kurikulum Pendidikan',
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        // 3. User Kabid A
        $idKabidA = uniqid('user-kabid-a-');
        $db->table('users')->insert([
            'id_user'    => $idKabidA,
            'id_opd'     => $idOpdA,
            'id_bidang'  => $idBidangA,
            'nama'       => 'dr. Kabid Kesehatan',
            'email'      => 'kabid.kes.' . uniqid() . '@siama.test',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // 4. Arsiparis A & B
        $idArsiparisA = uniqid('user-arsip-a-');
        $db->table('users')->insert([
            'id_user'    => $idArsiparisA,
            'id_opd'     => $idOpdA,
            'id_bidang'  => $idBidangA,
            'nama'       => 'Arsiparis Dinkes',
            'email'      => 'arsip.kes.' . uniqid() . '@siama.test',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $idArsiparisB = uniqid('user-arsip-b-');
        $db->table('users')->insert([
            'id_user'    => $idArsiparisB,
            'id_opd'     => $idOpdB,
            'id_bidang'  => $idBidangB,
            'nama'       => 'Arsiparis Disdik',
            'email'      => 'arsip.dik.' . uniqid() . '@siama.test',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // 5. Masukkan Arsip di Bidang A
        $idArsipA1 = uniqid('arsip-a1-');
        $db->table('arsip')->insert([
            'id_arsip'          => $idArsipA1,
            'id_opd'            => $idOpdA,
            'id_bidang'         => $idBidangA,
            'nomor_arsip'       => 'ARS/KES/001',
            'nama_arsip'        => 'Laporan Kesehatan Masyarakat 2026',
            'status_verifikasi' => 'Selesai',
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        // 6. Masukkan Arsip di Bidang B (OPD B)
        $idArsipB1 = uniqid('arsip-b1-');
        $db->table('arsip')->insert([
            'id_arsip'          => $idArsipB1,
            'id_opd'            => $idOpdB,
            'id_bidang'         => $idBidangB,
            'nomor_arsip'       => 'ARS/DIK/001',
            'nama_arsip'        => 'Kurikulum Sekolah 2026',
            'status_verifikasi' => 'Selesai',
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        // 7. Masukkan Berita Acara di OPD A & OPD B (keduanya status Draf_Kabid)
        $idBaA = uniqid('ba-a-');
        $nomorBaA = 'BA/DINKES/' . strtoupper(substr(uniqid(), 0, 5));
        $db->table('berita_acara')->insert([
            'id_berita_acara'    => $idBaA,
            'id_opd'             => $idOpdA,
            'id_pelaksana'       => $idArsiparisA,
            'nomor_ba'           => $nomorBaA,
            'tanggal_ba'         => date('Y-m-d'),
            'status_persetujuan' => 'Draf_Kabid',
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        $idBaB = uniqid('ba-b-');
        $nomorBaB = 'BA/DISDIK/' . strtoupper(substr(uniqid(), 0, 5));
        $db->table('berita_acara')->insert([
            'id_berita_acara'    => $idBaB,
            'id_opd'             => $idOpdB,
            'id_pelaksana'       => $idArsiparisB,
            'nomor_ba'           => $nomorBaB,
            'tanggal_ba'         => date('Y-m-d'),
            'status_persetujuan' => 'Draf_Kabid',
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        $sessKabidA = [
            'user_id'             => $idKabidA,
            'email'               => 'kabid.kes@siama.test',
            'nama_lengkap'        => 'dr. Kabid Kesehatan',
            'nama_role'           => 'Kepala_Bidang',
            'id_role'             => '4',
            'id_opd'              => $idOpdA,
            'id_bidang'           => $idBidangA,
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];

        $res = $this->withSession($sessKabidA)->get('/dashboard');
        $res->assertOK();
        $body = $res->getBody();

        // Verifikasi BA OPD A muncul, BA OPD B TIDAK boleh muncul
        $res->assertSee($nomorBaA);
        $res->assertSee('Arsiparis Dinkes');
        $this->assertStringNotContainsString($nomorBaB, $body);
        $this->assertStringNotContainsString('Arsiparis Disdik', $body);

        // Verifikasi Kolom Tabel Berita Acara Menanti Verifikasi Kabid
        $res->assertSee('Nomor Berita Acara');
        $res->assertSee('Pelaksana');
        $res->assertSee('Lihat Semua');

        // Verifikasi link detail BA
        $this->assertStringContainsString('/berita-acara/detail/' . $idBaA, $body);

        // Verifikasi update dinamis ketika arsip bertambah
        $idArsipA2 = uniqid('arsip-a2-');
        $db->table('arsip')->insert([
            'id_arsip'          => $idArsipA2,
            'id_opd'            => $idOpdA,
            'id_bidang'         => $idBidangA,
            'nomor_arsip'       => 'ARS/KES/002',
            'nama_arsip'        => 'Laporan Vaksinasi 2026',
            'status_verifikasi' => 'Selesai',
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        $res2 = $this->withSession($sessKabidA)->get('/dashboard');
        $res2->assertOK();
        // Menampilkan 2 arsip di bidang Anda
        $res2->assertSee('Total Arsip di Bidang Anda');
    }
}
