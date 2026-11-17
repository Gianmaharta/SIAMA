<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class AdminPemkabDashboardTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    private function sessAdminPemkab(): array
    {
        return [
            'user_id'             => 'admin-pemkab-test-id',
            'nama_lengkap'        => 'Administrator Pemkab',
            'email'               => 'admin@pemkab.test',
            'id_role'             => 1,
            'nama_role'           => 'Admin_Pemkab',
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];
    }

    public function testAdminPemkabAccessesDashboard()
    {
        $db = \Config\Database::connect();
        
        // Pastikan ada minimal 1 OPD dan 1 SPT aktif untuk menguji tampilan data
        $opd = $db->table('opd')->get(1)->getRowArray();
        $idOpd = $opd['id_opd'] ?? null;
        if (!$idOpd) {
            $idOpd = 'test-opd-' . time();
            $db->table('opd')->insert([
                'id_opd'     => $idOpd,
                'kode_opd'   => 'OPD-TEST',
                'nama_opd'   => 'Dinas Pengujian Pemkab',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $pimpinan = $db->table('users')->get(1)->getRowArray();
        $idPimpinan = $pimpinan['id_user'] ?? 'test-pimpinan-id';

        $db->table('spt')->insert([
            'id_spt'           => 'spt-test-' . time(),
            'id_opd'           => $idOpd,
            'nomor_spt'        => '090/TEST/' . time(),
            'tanggal_spt'      => date('Y-m-d'),
            'id_pimpinan'      => $idPimpinan,
            'perihal'          => 'Uji Coba Alih Media',
            'tanggal_mulai'    => date('Y-m-d'),
            'tanggal_selesai'  => date('Y-m-d', strtotime('+3 days')),
            'status'           => 'Aktif',
            'status_penugasan' => 'belum_ditugaskan',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        $result = $this->withSession($this->sessAdminPemkab())
                       ->get('/dashboard');

        $result->assertOK();

        // 1. Verifikasi Header
        $result->assertSee('Selamat Datang, Administrator Pemkab!');

        // 2. Verifikasi 5 Kartu Statistik Utama
        $result->assertSee('Total OPD');
        $result->assertSee('Total Pengguna');
        $result->assertSee('Total Arsip Global');
        $result->assertSee('Total SPT Global');
        $result->assertSee('Total Berita Acara Global');

        // 3. Verifikasi Bagian Ringkasan Pekerjaan Tertunda
        $result->assertSee('Ringkasan Pekerjaan Tertunda &amp; Butuh Tindak Lanjut');
        $result->assertSee('SPT Aktif');
        $result->assertSee('Menunggu Verifikasi');
        $result->assertSee('Tahapan yang Sedang Tertunda / Berjalan');

        // 4. Verifikasi Bagian Distribusi & Progres Alih Media per OPD
        $result->assertSee('Distribusi &amp; Progres Alih Media per OPD');
        $result->assertSee('Rincian Progres Alih Media per OPD');
        $result->assertSee('Progres Penyelesaian');

        // 5. Verifikasi Bagian Monitoring Workflow Pengarsipan
        $result->assertSee('Monitoring Workflow Pengarsipan');
        $result->assertSee('Nomor SPT');
        $result->assertSee('Tahapan Alur Saat Ini');
        $result->assertSee('Petugas / Role Penangan');
        $result->assertSee('Status Proses');

        // 6. Verifikasi Bagian Aktivitas Terbaru
        $result->assertSee('Aktivitas Terbaru');
    }

    public function testChartDataAjaxReturnsProgressBreakdown()
    {
        $result = $this->withSession($this->sessAdminPemkab())
                       ->get('/dashboard/chart-data');

        $result->assertOK();
        $json = json_decode($result->getJSON(), true);

        $this->assertEquals('success', $json['status']);
        $this->assertArrayHasKey('labels', $json);
        $this->assertArrayHasKey('values', $json);
        $this->assertArrayHasKey('opd_progress', $json);
        $this->assertIsArray($json['opd_progress']);

        if (!empty($json['opd_progress'])) {
            $first = $json['opd_progress'][0];
            $this->assertArrayHasKey('nama_opd', $first);
            $this->assertArrayHasKey('total_arsip', $first);
            $this->assertArrayHasKey('selesai_count', $first);
            $this->assertArrayHasKey('proses_count', $first);
            $this->assertArrayHasKey('menunggu_count', $first);
            $this->assertArrayHasKey('persentase', $first);
        }
    }

    public function testChartDataAjaxWithOpdAndPeriodFilter()
    {
        $db = \Config\Database::connect();
        $opd = $db->table('opd')->get(1)->getRowArray();
        $idOpd = $opd['id_opd'] ?? 'all';

        $result = $this->withSession($this->sessAdminPemkab())
                       ->get('/dashboard/chart-data?opd=' . urlencode($idOpd) . '&periode=all');

        $result->assertOK();
        $json = json_decode($result->getJSON(), true);

        $this->assertEquals('success', $json['status']);
        $this->assertArrayHasKey('opd_progress', $json);
    }

    public function testPendingWorkSummaryAccurateCalculationAndNoDoubleCounting()
    {
        $db = \Config\Database::connect();

        // 1. Buat OPD A (dengan pekerjaan aktif)
        $idOpdA = 'opd-pending-a-' . time();
        $namaOpdA = 'Dinas Lingkungan Hidup Uji ' . time();
        $db->table('opd')->insert([
            'id_opd'     => $idOpdA,
            'kode_opd'   => 'DLH-UJI',
            'nama_opd'   => $namaOpdA,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Buat user pimpinan
        $idPimpinan = 'user-pimp-' . time();
        $db->table('users')->insert([
            'id_user'    => $idPimpinan,
            'id_opd'     => $idOpdA,
            'nama'       => 'Pimpinan DLH',
            'email'      => 'pimpinan.' . time() . '@dlh.test',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // SPT 1 di OPD A: Status Aktif, belum_ditugaskan (Tahap 1: Penugasan Arsiparis)
        $idSpt1 = 'spt-pending-1-' . time();
        $nomorSpt1 = '090/DLH/1/' . time();
        $db->table('spt')->insert([
            'id_spt'           => $idSpt1,
            'id_opd'           => $idOpdA,
            'nomor_spt'        => $nomorSpt1,
            'tanggal_spt'      => date('Y-m-d'),
            'id_pimpinan'      => $idPimpinan,
            'perihal'          => 'Digitalisasi Dokumen Amdal',
            'tanggal_mulai'    => date('Y-m-d'),
            'tanggal_selesai'  => date('Y-m-d', strtotime('+7 days')),
            'status'           => 'Aktif',
            'status_penugasan' => 'belum_ditugaskan',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        // SPT 2 di OPD A: Status Aktif, ditugaskan, memiliki BA Draf_Kabid (Tahap 4: Verifikasi Kabid)
        $idSpt2 = 'spt-pending-2-' . time();
        $nomorSpt2 = '090/DLH/2/' . time();
        $db->table('spt')->insert([
            'id_spt'           => $idSpt2,
            'id_opd'           => $idOpdA,
            'nomor_spt'        => $nomorSpt2,
            'tanggal_spt'      => date('Y-m-d'),
            'id_pimpinan'      => $idPimpinan,
            'perihal'          => 'Digitalisasi Dokumen Kebersihan',
            'tanggal_mulai'    => date('Y-m-d'),
            'tanggal_selesai'  => date('Y-m-d', strtotime('+7 days')),
            'status'           => 'Aktif',
            'status_penugasan' => 'ditugaskan',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        // Berita Acara untuk SPT 2 (Draf_Kabid)
        $idBa2 = 'ba-pending-2-' . time();
        $nomorBa2 = 'BA/DLH/2/' . time();
        $db->table('berita_acara')->insert([
            'id_berita_acara'    => $idBa2,
            'id_opd'             => $idOpdA,
            'id_spt'             => $idSpt2,
            'nomor_ba'           => $nomorBa2,
            'tanggal_ba'         => date('Y-m-d'),
            'status_persetujuan' => 'Draf_Kabid',
            'id_pelaksana'       => $idPimpinan,
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        // Tambah 2 arsip di bawah SPT 2 yang berstatus Menunggu
        for ($i = 1; $i <= 2; $i++) {
            $db->table('arsip')->insert([
                'id_arsip'          => 'arsip-pending-' . $i . '-' . time(),
                'id_opd'            => $idOpdA,
                'id_spt'            => $idSpt2,
                'nomor_arsip'       => 'ARS/DLH/' . $i . '/' . time(),
                'nama_arsip'        => 'Berkas Sampel ' . $i,
                'status_verifikasi' => 'Menunggu',
                'created_at'        => date('Y-m-d H:i:s'),
            ]);
        }

        // SPT 3 di OPD A: SUDAH SELESAI (BA Selesai) -> TIDAK boleh dihitung sebagai pending
        $idSpt3 = 'spt-pending-3-' . time();
        $nomorSpt3 = '090/DLH/3/' . time();
        $db->table('spt')->insert([
            'id_spt'           => $idSpt3,
            'id_opd'           => $idOpdA,
            'nomor_spt'        => $nomorSpt3,
            'tanggal_spt'      => date('Y-m-d'),
            'id_pimpinan'      => $idPimpinan,
            'perihal'          => 'Digitalisasi Dokumen Lama Selesai',
            'tanggal_mulai'    => date('Y-m-d'),
            'tanggal_selesai'  => date('Y-m-d', strtotime('+7 days')),
            'status'           => 'Selesai',
            'status_penugasan' => 'selesai',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        $db->table('berita_acara')->insert([
            'id_berita_acara'    => 'ba-selesai-' . time(),
            'id_opd'             => $idOpdA,
            'id_spt'             => $idSpt3,
            'nomor_ba'           => 'BA/DLH/SELESAI/' . time(),
            'tanggal_ba'         => date('Y-m-d'),
            'status_persetujuan' => 'Selesai',
            'id_pelaksana'       => $idPimpinan,
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        // 2. Buat OPD B (TANPA pekerjaan aktif) -> TIDAK boleh masuk daftar pekerjaan tertunda
        $idOpdB = 'opd-clean-b-' . time();
        $namaOpdB = 'Dinas Tanpa Pekerjaan ' . time();
        $db->table('opd')->insert([
            'id_opd'     => $idOpdB,
            'kode_opd'   => 'CLEAN-B',
            'nama_opd'   => $namaOpdB,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Akses dashboard Admin Pemkab
        $result = $this->withSession($this->sessAdminPemkab())
                       ->get('/dashboard');

        $result->assertOK();

        // Verifikasi OPD A tampil di Ringkasan Pekerjaan Tertunda
        $result->assertSee($namaOpdA);
        $result->assertSee('DLH-UJI');

        // Verifikasi SPT Aktif: hanya 2 SPT (bukan 3 karena SPT 3 sudah selesai)
        $result->assertSee('2 SPT');

        // Verifikasi Total Belum Selesai: 2 Pekerjaan (SPT 1 + SPT 2, TIDAK terjadi double counting arsip)
        $result->assertSee('2 Pekerjaan');

        // Verifikasi tahapan aktual yang berjalan
        $result->assertSee('Penugasan Arsiparis');
        $result->assertSee('Verifikasi Kepala Bidang');

        // Verifikasi tombol Pantau Workflow membawa data-opd-id yang valid
        $this->assertStringContainsString('data-opd-id="' . $idOpdA . '"', $result->getBody());

        // Verifikasi OPD B (tanpa pekerjaan aktif) TIDAK ditampilkan di tabel daftar pekerjaan tertunda
        preg_match('/<div class="panel-card pending-work-panel">(.*?)<!-- Panel 2/s', $result->getBody(), $matches);
        $pendingPanelHtml = $matches[1] ?? '';
        $this->assertStringContainsString($namaOpdA, $pendingPanelHtml);
        $this->assertStringNotContainsString($namaOpdB, $pendingPanelHtml);
    }
}
