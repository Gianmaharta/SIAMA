<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class WorkflowStateTransitionTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    private function getSessionData(string $role, string $userId, ?string $idOpd = null): array
    {
        return [
            'user_id'             => $userId,
            'nama_lengkap'        => 'User ' . $role,
            'email'               => strtolower($role) . '@siama.test',
            'id_role'             => ($role === 'Admin_Pemkab' ? 1 : ($role === 'Pimpinan' ? 2 : ($role === 'Kepala_Bidang' ? 3 : 5))),
            'nama_role'           => $role,
            'id_opd'              => $idOpd,
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];
    }

    public function testCompleteWorkflowTransitions()
    {
        $db = \Config\Database::connect();

        // 1. Dapatkan atau buat OPD untuk pengujian
        $opd = $db->table('opd')->get(1)->getRowArray();
        $idOpd = $opd['id_opd'] ?? 'test-opd-wf';
        if (!$opd) {
            $db->table('opd')->insert([
                'id_opd'     => $idOpd,
                'kode_opd'   => 'OPD-WF',
                'nama_opd'   => 'Dinas Pengujian Workflow',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $idTestSpt = 'spt-wf-test-' . time();
        $nomorSpt = 'SPT/WF/' . time();

        $anyUser = $db->table('users')->get(1)->getRowArray();
        $realUserId = $anyUser['id_user'] ?? null;
        if (!$realUserId) {
            $realUserId = 'user-test-' . time();
            $db->table('users')->insert([
                'id_user'    => $realUserId,
                'nama'       => 'Test User',
                'email'      => 'test' . time() . '@siama.test',
                'password'   => password_hash('password123', PASSWORD_BCRYPT),
                'id_opd'     => $idOpd,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $idPimpinan  = $realUserId;
        $idArsiparis = $realUserId;
        $idKabid     = $realUserId;

        // =========================================================================
        // TAHAP 1: Pimpinan menerbitkan SPT
        // =========================================================================
        $db->table('spt')->insert([
            'id_spt'           => $idTestSpt,
            'id_opd'           => $idOpd,
            'nomor_spt'        => $nomorSpt,
            'tanggal_spt'      => date('Y-m-d'),
            'id_pimpinan'      => $idPimpinan,
            'perihal'          => 'Uji Alur Workflow Lengkap',
            'tanggal_mulai'    => date('Y-m-d'),
            'tanggal_selesai'  => date('Y-m-d', strtotime('+7 days')),
            'status'           => 'Aktif',
            'status_penugasan' => 'belum_ditugaskan',
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        // Cek endpoint workflow-data
        $result = $this->withSession($this->getSessionData('Admin_Pemkab', $idPimpinan, $idOpd))
            ->get('/dashboard/workflow-data');

        $result->assertStatus(200);
        $json = json_decode($result->getJSON(), true);
        $this->assertNotEmpty($json['workflow_monitoring_list']);

        // Cari SPT di list
        $wfItem = null;
        foreach ($json['workflow_monitoring_list'] as $item) {
            if ($item['id_spt'] === $idTestSpt) {
                $wfItem = $item;
                break;
            }
        }

        $this->assertNotNull($wfItem);
        $this->assertEquals(1, $wfItem['tahap_num'], 'Harus Tahap 1 saat SPT baru terbit');
        $this->assertEquals('Menunggu Penugasan Arsiparis', $wfItem['status_proses']);

        // =========================================================================
        // TAHAP 2: Kabid menugaskan Arsiparis
        // =========================================================================
        $idAssignment = 'assign-wf-' . time();
        $db->table('spt_assignments')->insert([
            'id_assignment' => $idAssignment,
            'id_spt'        => $idTestSpt,
            'id_user'       => $idArsiparis,
            'assigned_by'   => $idKabid,
            'status'        => 'pending',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        $db->table('spt')->where('id_spt', $idTestSpt)->update([
            'status_penugasan' => 'ditugaskan',
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        $result2 = $this->withSession($this->getSessionData('Admin_Pemkab', $idPimpinan, $idOpd))
            ->get('/dashboard/workflow-data');
        $json2 = json_decode($result2->getJSON(), true);

        $wfItem2 = null;
        foreach ($json2['workflow_monitoring_list'] as $item) {
            if ($item['id_spt'] === $idTestSpt) {
                $wfItem2 = $item;
                break;
            }
        }

        $this->assertNotNull($wfItem2);
        $this->assertEquals(2, $wfItem2['tahap_num'], 'Harus Tahap 2 saat arsiparis sudah ditugaskan');
        $this->assertEquals('Menunggu Mulai Alih Media', $wfItem2['status_proses']);

        // =========================================================================
        // TAHAP 4: Arsiparis mengunggah arsip (Menunggu Verifikasi)
        // =========================================================================
        $idArsip = 'arsip-wf-' . time();
        $db->table('arsip')->insert([
            'id_arsip'          => $idArsip,
            'id_spt'            => $idTestSpt,
            'id_opd'            => $idOpd,
            'nomor_arsip'       => 'ARSIP/WF/' . time(),
            'nama_arsip'        => 'Berkas Uji Workflow',
            'status_verifikasi' => 'Menunggu',
            'id_user_upload'    => $idArsiparis,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);
        $db->table('spt')->where('id_spt', $idTestSpt)->update([
            'status_penugasan' => 'selesai',
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        $result3 = $this->withSession($this->getSessionData('Admin_Pemkab', $idPimpinan, $idOpd))
            ->get('/dashboard/workflow-data');
        $json3 = json_decode($result3->getJSON(), true);

        $wfItem3 = null;
        foreach ($json3['workflow_monitoring_list'] as $item) {
            if ($item['id_spt'] === $idTestSpt) {
                $wfItem3 = $item;
                break;
            }
        }

        $this->assertNotNull($wfItem3);
        $this->assertEquals(4, $wfItem3['tahap_num'], 'Harus Tahap 4 saat ada arsip menunggu verifikasi');
        $this->assertEquals('Menunggu Verifikasi Arsip', $wfItem3['status_proses']);

        // Periksa juga pending work summary
        $pendingOpd = null;
        foreach ($json3['pending_work_summary'] as $p) {
            if ($p['id_opd'] === $idOpd) {
                $pendingOpd = $p;
                break;
            }
        }
        $this->assertNotNull($pendingOpd);
        $this->assertGreaterThan(0, $pendingOpd['menunggu_verifikasi']);
        $tahapanLabels = array_column($pendingOpd['tahapan_list'], 'label');
        $this->assertContains('Verifikasi Kepala Bidang', $tahapanLabels, 'Ringkasan pekerjaan harus menampilkan Verifikasi Kepala Bidang');

        // =========================================================================
        // TAHAP 5 (Persetujuan Pimpinan): Buat BA dengan status Draf_Pimpinan
        // =========================================================================
        $idBa = 'ba-wf-' . time();
        $db->table('berita_acara')->insert([
            'id_berita_acara'    => $idBa,
            'id_opd'             => $idOpd,
            'id_spt'             => $idTestSpt,
            'nomor_ba'           => 'BA/WF/' . time(),
            'tanggal_ba'         => date('Y-m-d'),
            'status_persetujuan' => 'Draf_Pimpinan',
            'id_pelaksana'       => $idArsiparis,
            'id_kabid'           => $idKabid,
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);
        $db->table('berita_acara_detail')->insert([
            'id_berita_acara' => $idBa,
            'id_arsip'        => $idArsip,
        ]);
        $db->table('arsip')->where('id_arsip', $idArsip)->update([
            'status_verifikasi' => 'Selesai',
            'id_berita_acara'   => $idBa,
        ]);

        $result4 = $this->withSession($this->getSessionData('Admin_Pemkab', $idPimpinan, $idOpd))
            ->get('/dashboard/workflow-data');
        $json4 = json_decode($result4->getJSON(), true);

        $wfItem4 = null;
        foreach ($json4['workflow_monitoring_list'] as $item) {
            if ($item['id_spt'] === $idTestSpt) {
                $wfItem4 = $item;
                break;
            }
        }

        $this->assertNotNull($wfItem4);
        $this->assertEquals(5, $wfItem4['tahap_num'], 'Harus Tahap 5 saat BA menunggu TTD pimpinan');
        $this->assertEquals('Menunggu TTD Pimpinan', $wfItem4['status_proses']);

        // =========================================================================
        // TAHAP 5 (SELESAI): Pimpinan menandatangani Berita Acara
        // =========================================================================
        $db->table('berita_acara')->where('id_berita_acara', $idBa)->update([
            'status_persetujuan' => 'Selesai',
            'id_pimpinan'        => $idPimpinan,
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);
        $db->table('spt')->where('id_spt', $idTestSpt)->update([
            'status'           => 'Selesai',
            'status_penugasan' => 'selesai',
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        $result5 = $this->withSession($this->getSessionData('Admin_Pemkab', $idPimpinan, $idOpd))
            ->get('/dashboard/workflow-data');
        $json5 = json_decode($result5->getJSON(), true);

        $wfItem5 = null;
        foreach ($json5['workflow_monitoring_list'] as $item) {
            if ($item['id_spt'] === $idTestSpt) {
                $wfItem5 = $item;
                break;
            }
        }

        $this->assertNotNull($wfItem5);
        $this->assertEquals(5, $wfItem5['tahap_num']);
        $this->assertEquals('Selesai', $wfItem5['status_proses']);

        // Clean up test data
        $db->table('berita_acara_detail')->where('id_berita_acara', $idBa)->delete();
        $db->table('berita_acara')->where('id_berita_acara', $idBa)->delete();
        $db->table('arsip')->where('id_arsip', $idArsip)->delete();
        $db->table('spt_assignments')->where('id_assignment', $idAssignment)->delete();
        $db->table('spt')->where('id_spt', $idTestSpt)->delete();
    }
}
