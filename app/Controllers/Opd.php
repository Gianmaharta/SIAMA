<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OpdModel;

class Opd extends BaseController
{
    protected $opdModel;

    public function __construct()
    {
        $this->opdModel = new OpdModel();
        helper('logger');
    }

    public function index()
    {
        $opds = $this->opdModel->orderBy('kode_opd', 'ASC')->findAll();
        
        $totalOpd = count($opds);
        $activeOpd = count(array_filter($opds, fn($item) => (int)($item['is_active'] ?? 0) === 1));
        $inactiveOpd = count(array_filter($opds, fn($item) => (int)($item['is_active'] ?? 0) === 0));
        $nextKodeOpd = $this->opdModel->generateNextKodeOpd();

        $data = [
            'opds'        => $opds,
            'totalOpd'    => $totalOpd,
            'activeOpd'   => $activeOpd,
            'inactiveOpd' => $inactiveOpd,
            'nextKodeOpd' => $nextKodeOpd,
        ];

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status'       => 'success',
                'total_opd'    => $totalOpd,
                'active_opd'   => $activeOpd,
                'inactive_opd' => $inactiveOpd,
                'data'         => $opds,
            ]);
        }

        return view('opd/index', $data);
    }

    /**
     * Endpoint Server-Side DataTables untuk Master OPD (GET /opd/datatable)
     */
    public function datatable()
    {
        $draw   = (int) ($this->request->getGet('draw') ?? 1);
        $lengthParam = $this->request->getGet('length') ?: $this->request->getGet('per_page') ?: 10;
        $length = ($lengthParam === 'all') ? 500 : max(1, min(500, (int) $lengthParam));
        $start  = $this->request->getGet('start') !== null
            ? max(0, (int) $this->request->getGet('start'))
            : (max(1, (int) ($this->request->getGet('page') ?: 1)) - 1) * $length;

        $searchParam = $this->request->getGet('search');
        $search = is_array($searchParam)
            ? trim((string) ($searchParam['value'] ?? ''))
            : trim((string) ($searchParam ?? ''));

        $statusFilter = trim((string) ($this->request->getGet('is_active') ?? 'all'));

        $db = \Config\Database::connect();
        $recordsTotal = (int) $db->table('opd')->countAllResults();

        $builder = $db->table('opd');
        if ($search !== '') {
            $builder->groupStart()
                ->like('nama_opd', $search)
                ->orLike('kode_opd', $search)
                ->orLike('id_opd', $search)
                ->groupEnd();
        }
        if ($statusFilter === '1' || $statusFilter === '0') {
            $builder->where('is_active', (int) $statusFilter);
        }

        $recordsFiltered = (int) (clone $builder)->countAllResults();
        $rows = $builder->orderBy('kode_opd', 'ASC')
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status'          => 'success',
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $rows,
        ]);
    }

    public function create()
    {
        $data['nextKodeOpd'] = $this->opdModel->generateNextKodeOpd();
        return view('opd/create', $data);
    }

    public function store()
    {
        $kodeOpd = trim($this->request->getPost('kode_opd') ?? '');
        $namaOpd = trim($this->request->getPost('nama_opd') ?? '');
        $kuota   = (int)($this->request->getPost('kuota_storage_mb') ?: 1000);
        $active  = (int)($this->request->getPost('is_active') ?? 1);

        if (empty($namaOpd)) {
            return redirect()->back()->withInput()->with('error', 'Nama OPD wajib diisi.');
        }

        // Auto-generate kode_opd dari sistem
        if (empty($kodeOpd)) {
            $kodeOpd = $this->opdModel->generateNextKodeOpd();
        } else {
            $existing = $this->opdModel->where('kode_opd', $kodeOpd)->first();
            if ($existing) {
                $kodeOpd = $this->opdModel->generateNextKodeOpd();
            }
        }

        $data = [
            'kode_opd'         => $kodeOpd,
            'nama_opd'         => $namaOpd,
            'kuota_storage_mb' => $kuota,
            'is_active'        => $active,
        ];

        if (!$this->opdModel->insert($data)) {
            $errors = $this->opdModel->errors();
            $msg = !empty($errors) ? implode(', ', $errors) : 'Gagal menyimpan data OPD.';
            return redirect()->back()->withInput()->with('error', $msg);
        }

        log_activity('OPD', 'CREATE', "Menambahkan data master OPD baru: {$namaOpd} ({$kodeOpd})");

        return redirect()->to('/opd')->with('success', "Data OPD '{$namaOpd}' ({$kodeOpd}) berhasil ditambahkan.");
    }

    /**
     * Sinkronisasi Master OPD langsung dari API SIMPEG/SSO Pemkab Buleleng (`POST /opd/sync-api`)
     */
    public function syncApi()
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(120);
        }
        try {
            $syncService = new \App\Services\SimpegSyncService();
            $stats = $syncService->syncOpd();

            $msg = sprintf(
                'Sinkronisasi Master OPD dari API berhasil: %d data diproses (%d OPD baru ditambahkan, %d OPD diperbarui).',
                $stats['total_api'],
                $stats['inserted'],
                $stats['updated']
            );
            log_activity('OPD', 'SYNC_API', $msg);

            if ($this->wantsJson() || $this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => $msg,
                    'data'    => $stats,
                ]);
            }

            return redirect()->to('/opd')->with('success', $msg);
        } catch (\Throwable $e) {
            if ($this->wantsJson() || $this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => $e->getMessage(),
                ])->setStatusCode(500);
            }
            return redirect()->to('/opd')->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $data['opd'] = $this->opdModel->find($id);

        if (!$data['opd']) {
            return redirect()->to('/opd')->with('error', 'Data OPD tidak ditemukan.');
        }

        return view('opd/edit', $data);
    }

    public function update($id)
    {
        $opd = $this->opdModel->find($id);
        if (!$opd) {
            return redirect()->to('/opd')->with('error', 'Data OPD tidak ditemukan.');
        }

        $kodeOpd = trim($this->request->getPost('kode_opd') ?? '');
        $namaOpd = trim($this->request->getPost('nama_opd') ?? '');
        $kuota   = (int)($this->request->getPost('kuota_storage_mb') ?: 1000);
        $active  = (int)($this->request->getPost('is_active') ?? 0);

        if (empty($kodeOpd) || empty($namaOpd)) {
            return redirect()->back()->withInput()->with('error', 'Kode OPD dan Nama OPD wajib diisi.');
        }

        $duplicate = $this->opdModel->where('kode_opd', $kodeOpd)->where('id_opd !=', $id)->first();
        if ($duplicate) {
            return redirect()->back()->withInput()->with('error', "Kode OPD '{$kodeOpd}' sudah digunakan oleh OPD lain.");
        }

        $data = [
            'id_opd'           => $id,
            'kode_opd'         => $kodeOpd,
            'nama_opd'         => $namaOpd,
            'kuota_storage_mb' => $kuota,
            'is_active'        => $active,
        ];

        if (!$this->opdModel->save($data)) {
            $errors = $this->opdModel->errors();
            $msg = !empty($errors) ? implode(', ', $errors) : 'Gagal memperbarui data OPD.';
            return redirect()->back()->withInput()->with('error', $msg);
        }

        log_activity('OPD', 'UPDATE', "Memperbarui data master OPD: {$namaOpd} ({$kodeOpd})", $opd, $data);

        return redirect()->to('/opd')->with('success', "Data OPD '{$namaOpd}' berhasil diperbarui.");
    }

    public function delete($id)
    {
        $opd = $this->opdModel->find($id);
        if (!$opd) {
            return redirect()->to('/opd')->with('error', 'Data OPD tidak ditemukan.');
        }

        // Cek relasi child data (Bidang, User, Arsip) untuk menjaga integritas data
        $hasBidang = $this->opdModel->db->table('bidang')->where('id_opd', $id)->countAllResults() > 0;
        $hasUser   = $this->opdModel->db->table('users')->where('id_opd', $id)->countAllResults() > 0;
        $hasArsip  = $this->opdModel->db->table('arsip')->where('id_opd', $id)->countAllResults() > 0;

        if ($hasBidang || $hasUser || $hasArsip) {
            return redirect()->to('/opd')->with('error', 'Gagal menghapus OPD. Pastikan OPD ini tidak memiliki relasi data (seperti Bidang, User, atau Arsip).');
        }

        try {
            $this->opdModel->delete($id);
            log_activity('OPD', 'DELETE', "Menghapus data master OPD: {$opd['nama_opd']} ({$opd['kode_opd']})");
            return redirect()->to('/opd')->with('success', "Data OPD '{$opd['nama_opd']}' berhasil dihapus.");
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/opd')->with('error', 'Gagal menghapus OPD. Pastikan OPD ini tidak memiliki relasi data (seperti Bidang, User, atau Arsip).');
        } catch (\Exception $e) {
            return redirect()->to('/opd')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

