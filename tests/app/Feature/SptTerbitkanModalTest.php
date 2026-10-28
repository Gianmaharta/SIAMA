<?php

namespace Tests\App\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class SptTerbitkanModalTest extends CIUnitTestCase
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

    protected function sess(string $role, string $opd = 'opd-1'): array
    {
        return [
            'user_id'             => 'user-pimpinan-1',
            'email'               => 'pimpinan@siama.local',
            'nama_lengkap'        => 'Kepala Dinas Test',
            'nama_role'           => $role,
            'id_role'             => '2',
            'id_opd'              => $opd,
            'id_bidang'           => null,
            'isLoggedIn'          => true,
            'is_default_password' => 0,
        ];
    }

    public function testPimpinanSeesTerbitkanSptModalInSptIndex()
    {
        $result = $this->withSession($this->sess('Pimpinan'))
                       ->get('/spt');

        $result->assertOK();
        $result->assertSee('Terbitkan SPT');
        $result->assertSee('modalTerbitkanSpt');
        $result->assertSee('NOMOR SPT');
        $result->assertSee('TANGGAL TERBIT SPT');
        $result->assertSee('PERIHAL / KETERANGAN PENUGASAN');
        $result->assertSee('TANGGAL MULAI PELAKSANAAN');
        $result->assertSee('TANGGAL SELESAI PELAKSANAAN');
        $result->assertSee('Unggah Dokumen SPT (PDF)');
    }

    public function testDashboardDoesNotHaveTerbitkanSptButton()
    {
        $result = $this->withSession($this->sess('Pimpinan'))
                       ->get('/dashboard');

        $result->assertOK();
        $body = $result->getBody();
        $this->assertStringNotContainsString('class="spt-btn-create', $body);
        $this->assertStringNotContainsString('modalTerbitkanSpt', $body);
    }

    public function testArsiparisDoesNotSeeTerbitkanSptButton()
    {
        $result = $this->withSession($this->sess('Arsiparis'))
                       ->get('/spt');

        $result->assertOK();
        $body = $result->getBody();
        // Arsiparis should not have the button to create/terbitkan SPT
        $this->assertStringNotContainsString('class="spt-btn-create', $body);
    }

    public function testStoreRejectsNonPimpinan()
    {
        $result = $this->withSession($this->sess('Arsiparis'))
                       ->post('/spt/store', [
                           'nomor_spt'       => 'SPT/TEST/001',
                           'tanggal_spt'     => '2026-10-28',
                           'perihal'         => 'Test',
                           'tanggal_mulai'   => '2026-10-28',
                           'tanggal_selesai' => '2026-11-04',
                       ]);

        // RoleFilter langsung memblokir Arsiparis dan redirect ke dashboard
        $result->assertRedirect();
    }

    public function testAssignRejectsNonKepalaBidang()
    {
        // Admin_OPD harus ditolak mengakses assign
        $resultAdminOpd = $this->withSession($this->sess('Admin_OPD'))
                               ->get('/spt/assign/dummy-id');
        $resultAdminOpd->assertRedirectTo('/dashboard');

        // Pimpinan juga harus ditolak
        $resultPimpinan = $this->withSession($this->sess('Pimpinan'))
                               ->get('/spt/assign/dummy-id');
        $resultPimpinan->assertRedirectTo('/dashboard');

        // Arsiparis juga harus ditolak
        $resultArsiparis = $this->withSession($this->sess('Arsiparis'))
                                ->get('/spt/assign/dummy-id');
        $resultArsiparis->assertRedirectTo('/dashboard');
    }

    public function testProcessAssignRejectsNonKepalaBidang()
    {
        // Admin_OPD submit assignment harus ditolak
        $result = $this->withSession($this->sess('Admin_OPD'))
                       ->post('/spt/process-assign/dummy-id', [
                           'id_user' => 'dummy-user-id'
                       ]);
        $result->assertRedirectTo('/dashboard');
    }
}

