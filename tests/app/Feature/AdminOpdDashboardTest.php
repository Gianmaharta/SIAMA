<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class AdminOpdDashboardTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    private function getTestOpdId(): string
    {
        $db = \Config\Database::connect();
        $opd = $db->table('opd')->get(1)->getRowArray();
        if (!empty($opd['id_opd'])) {
            return $opd['id_opd'];
        }

        $idOpd = 'opd-test-' . time();
        $db->table('opd')->insert([
            'id_opd'     => $idOpd,
            'kode_opd'   => 'OPD-TEST',
            'nama_opd'   => 'Dinas Komunikasi dan Informatika',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return $idOpd;
    }

    private function sessAdminOpd(string $idOpd): array
    {
        return [
            'user_id'             => 'admin-opd-test-id',
            'nama_lengkap'        => 'Administrator OPD Kominfo',
            'email'               => 'admin.opd@pemkab.test',
            'id_role'             => 3,
            'nama_role'           => 'Admin_OPD',
            'id_opd'              => $idOpd,
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];
    }

    public function testAdminOpdAccessesDashboardSuccessfully()
    {
        $db = \Config\Database::connect();
        $idOpd = $this->getTestOpdId();

        // Siapkan data pengujian di OPD ini
        // 1. Bidang
        $idBidang = 'bidang-test-' . time();
        $db->table('bidang')->insert([
            'id_bidang'   => $idBidang,
            'id_opd'      => $idOpd,
            'nama_bidang' => 'Bidang Pengelolaan Informasi',
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        // 2. SPT
        $idSpt = 'spt-opd-test-' . time();
        $nomorSpt = '090/OPD/' . time();
        $db->table('spt')->insert([
            'id_spt'           => $idSpt,
            'id_opd'           => $idOpd,
            'nomor_spt'        => $nomorSpt,
            'tanggal_spt'      => date('Y-m-d'),
            'perihal'          => 'Alih Media Arsip Dinamis OPD',
            'tanggal_mulai'    => date('Y-m-d'),
            'tanggal_selesai'  => date('Y-m-d', strtotime('+5 days')),
            'status'           => 'Aktif',
            'status_penugasan' => 'belum_ditugaskan',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        // 3. Arsip terkait SPT di OPD ini
        $idArsip = 'arsip-opd-test-' . time();
        $nomorArsip = 'ARS/OPD/' . time();
        $db->table('arsip')->insert([
            'id_arsip'          => $idArsip,
            'id_opd'            => $idOpd,
            'id_bidang'         => $idBidang,
            'id_spt'            => $idSpt,
            'nomor_arsip'       => $nomorArsip,
            'nama_arsip'        => 'Berkas Program Digitalisasi 2025',
            'kurun_waktu'       => '2025',
            'jumlah'            => 1,
            'status_verifikasi' => 'Proses',
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        $result = $this->withSession($this->sessAdminOpd($idOpd))
                       ->get('/dashboard');

        $result->assertOK();

        // 1. Verifikasi Header & Salam
        $result->assertSee('Selamat Datang, Administrator OPD Kominfo!');

        // 2. Verifikasi 3 Kartu Statistik Utama
        $result->assertSee('Total Pengguna di OPD Anda');
        $result->assertSee('Total Bidang di OPD Anda');
        $result->assertSee('Total Arsip di OPD Anda');

        // 3. Verifikasi Bagian Ringkasan Proses Alih Media
        $result->assertSee('Ringkasan Proses Alih Media');
        $result->assertSee('Selesai Diarsipkan');
        $result->assertSee('Sedang Diproses');
        $result->assertSee('Menunggu Verifikasi');
        $result->assertSee('Belum Selesai');
        $result->assertSee('Progres Penyelesaian Pengarsipan:');

        // 4. Verifikasi Bagian Monitoring Workflow Pengarsipan OPD
        $result->assertSee('Monitoring Workflow Pengarsipan');
        $result->assertSee($nomorSpt);
        $result->assertSee('Nomor SPT');
        $result->assertSee('Tanggal SPT');
        $result->assertSee('Tahapan Alur Saat Ini');
        $result->assertSee('Petugas / Role Penanganan');
        $result->assertSee('Status Proses');

        // 5. Verifikasi Bagian Pengguna OPD & Bidang di OPD Anda
        $result->assertSee('Pengguna OPD');
        $result->assertSee('Bidang di OPD Anda');
        $result->assertSee('Bidang Pengelolaan Informasi');

        // 6. Verifikasi Bagian Daftar Arsip Terbaru & SPT Terkait
        $result->assertSee('Daftar Arsip Terbaru');
        $result->assertSee($nomorArsip);
        $result->assertSee('SPT Terkait');
        $result->assertSee($nomorSpt);
    }

    public function testAdminOpdOnlySeesOwnOpdData()
    {
        $db = \Config\Database::connect();
        $idOpd1 = $this->getTestOpdId();

        // Buat OPD kedua
        $idOpd2 = 'opd-other-' . time();
        $db->table('opd')->insert([
            'id_opd'     => $idOpd2,
            'kode_opd'   => 'OPD-OTHER',
            'nama_opd'   => 'Dinas Kesehatan Lain',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Buat SPT milik OPD kedua
        $nomorSptOther = 'SPT-SECRET-OTHER-' . time();
        $db->table('spt')->insert([
            'id_spt'           => 'spt-other-' . time(),
            'id_opd'           => $idOpd2,
            'nomor_spt'        => $nomorSptOther,
            'tanggal_spt'      => date('Y-m-d'),
            'perihal'          => 'SPT Rahasia Dinas Lain',
            'tanggal_mulai'    => date('Y-m-d'),
            'tanggal_selesai'  => date('Y-m-d', strtotime('+5 days')),
            'status'           => 'Aktif',
            'status_penugasan' => 'belum_ditugaskan',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        // Login sebagai Admin OPD 1
        $result = $this->withSession($this->sessAdminOpd($idOpd1))
                       ->get('/dashboard');

        $result->assertOK();

        // Pastikan nomor SPT milik OPD 2 TIDAK muncul di dashboard Admin OPD 1
        $result->assertDontSee($nomorSptOther);
    }

    public function testWorkflowPetugasAndRoleDisplayDistinction()
    {
        $db = \Config\Database::connect();
        $idOpd = $this->getTestOpdId();

        // 1. Buat user Arsiparis dengan nama sebenarnya "Budi Santoso"
        $idArsiparis = 'user-arsiparis-' . time();
        $db->table('users')->insert([
            'id_user'    => $idArsiparis,
            'id_opd'     => $idOpd,
            'nama'       => 'Budi Santoso',
            'email'      => 'budi.' . time() . '@test.com',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $roleArsiparis = $db->table('roles')->where('nama_role', 'Arsiparis')->get()->getRowArray();
        if (!empty($roleArsiparis['id_role'])) {
            $db->table('user_roles')->insert([
                'id_user' => $idArsiparis,
                'id_role' => $roleArsiparis['id_role'],
            ]);
        }

        // 2. Buat SPT yang sudah ditugaskan kepada Budi Santoso
        $idSptAssigned = 'spt-assigned-' . time();
        $nomorSptAssigned = 'SPT/ASSIGNED/' . time();
        $db->table('spt')->insert([
            'id_spt'           => $idSptAssigned,
            'id_opd'           => $idOpd,
            'nomor_spt'        => $nomorSptAssigned,
            'tanggal_spt'      => date('Y-m-d'),
            'perihal'          => 'Alih Media Arsip Kepegawaian',
            'tanggal_mulai'    => date('Y-m-d'),
            'tanggal_selesai'  => date('Y-m-d', strtotime('+7 days')),
            'status'           => 'Aktif',
            'status_penugasan' => 'ditugaskan',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);
        $adminUser = $db->table('users')->get(1)->getRowArray();
        $assignedBy = $adminUser['id_user'] ?? $idArsiparis;

        $db->table('spt_assignments')->insert([
            'id_assignment' => 'assign-' . time(),
            'id_spt'        => $idSptAssigned,
            'id_user'       => $idArsiparis,
            'assigned_by'   => $assignedBy,
            'status'        => 'assigned',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        $result = $this->withSession($this->sessAdminOpd($idOpd))
                       ->get('/dashboard');

        $result->assertOK();

        // Verifikasi nama pengguna sebenarnya tampil
        $result->assertSee('Budi Santoso');
        // Verifikasi role tampil
        $result->assertSee('Arsiparis');
    }
}
