<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BidangModel;
use App\Models\OpdModel;

class Bidang extends BaseController
{
    protected $bidangModel;
    protected $opdModel;

    public function __construct()
    {
        $this->bidangModel = new BidangModel();
        $this->opdModel = new OpdModel();
        helper('logger');
    }

    public function index()
    {
        $session = session();
        $nama_role = $session->get('nama_role');
        $id_opd  = $session->get('id_opd');

        // Jika Admin_OPD, tampilkan hanya bidang di OPD-nya
        if ($nama_role === 'Admin_OPD') {
            $data['bidangs'] = $this->bidangModel->getBidangWithOpd($id_opd);
            $data['opds'] = $this->opdModel->where('id_opd', $id_opd)->findAll();
            $data['id_opd_locked'] = $id_opd;
        } else {
            // Jika Admin_Pemkab, tampilkan semua
            $data['bidangs'] = $this->bidangModel->getBidangWithOpd();
            $data['opds'] = $this->opdModel->orderBy('nama_opd', 'ASC')->findAll();
            $data['id_opd_locked'] = null;
        }

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'total'   => count($data['bidangs']),
                'data'    => $data['bidangs'],
            ]);
        }

        return view('bidang/index', $data);
    }

    /**
     * Endpoint Server-Side DataTables untuk Master Bidang (GET /bidang/datatable)
     */
    public function datatable()
    {
        $session   = session();
        $nama_role = $session->get('nama_role');
        $id_opd    = $session->get('id_opd');

        $draw   = (int) ($this->request->getGet('draw') ?? 1);
        $length = max(1, min(100, (int) ($this->request->getGet('length') ?: $this->request->getGet('per_page') ?: 10)));
        $start  = $this->request->getGet('start') !== null
            ? max(0, (int) $this->request->getGet('start'))
            : (max(1, (int) ($this->request->getGet('page') ?: 1)) - 1) * $length;

        $searchParam = $this->request->getGet('search');
        $search = is_array($searchParam)
            ? trim((string) ($searchParam['value'] ?? ''))
            : trim((string) ($searchParam ?? ''));

        $sortMap = [
            'id_bidang'   => 'bidang.id_bidang',
            'nama_opd'    => 'opd.nama_opd',
            'nama_bidang' => 'bidang.nama_bidang',
        ];
        $sortBy  = (string) ($this->request->getGet('sort_by') ?: 'nama_opd');
        $sortCol = $sortMap[$sortBy] ?? 'opd.nama_opd';
        $sortDir = strtoupper((string) ($this->request->getGet('sort_dir') ?: 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

        $db = \Config\Database::connect();
        $baseBuilder = $db->table('bidang')
            ->select('bidang.*, opd.nama_opd')
            ->join('opd', 'opd.id_opd = bidang.id_opd', 'left');

        if ($nama_role === 'Admin_OPD' && !empty($id_opd)) {
            $baseBuilder->where('bidang.id_opd', $id_opd);
        }

        $recordsTotal = (int) (clone $baseBuilder)->countAllResults();

        if ($search !== '') {
            $baseBuilder->groupStart()
                ->like('bidang.id_bidang', $search)
                ->orLike('bidang.kode_bidang', $search)
                ->orLike('opd.nama_opd', $search)
                ->orLike('bidang.nama_bidang', $search)
                ->groupEnd();
        }

        $recordsFiltered = (int) (clone $baseBuilder)->countAllResults();
        $rows = $baseBuilder->orderBy($sortCol, $sortDir)
            ->orderBy('bidang.nama_bidang', 'ASC')
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
        $session = session();
        $nama_role = $session->get('nama_role');
        $id_opd  = $session->get('id_opd');

        if ($nama_role === 'Admin_OPD') {
            // Kunci pada OPD user tersebut
            $data['opds'] = $this->opdModel->where('id_opd', $id_opd)->findAll();
            $data['id_opd_locked'] = $id_opd;
        } else {
            // Bebas pilih OPD
            $data['opds'] = $this->opdModel->orderBy('nama_opd', 'ASC')->findAll();
            $data['id_opd_locked'] = null;
        }

        return view('bidang/create', $data);
    }

    public function store()
    {
        $session = session();
        $nama_role = $session->get('nama_role');
        $id_opd  = $session->get('id_opd');

        $selectedOpd = ($nama_role === 'Admin_OPD') ? $id_opd : $this->request->getPost('id_opd');
        $namaBidang = trim((string) $this->request->getPost('nama_bidang'));

        $data = [
            'id_opd'      => $selectedOpd,
            'nama_bidang' => $namaBidang
        ];

        if (!$this->bidangModel->insert($data)) {
            $errors = $this->bidangModel->errors();
            $errMsg = !empty($errors) ? implode(' ', $errors) : 'Gagal menyimpan data Bidang.';
            return redirect()->back()->withInput()->with('error', $errMsg);
        }

        log_activity('Bidang', 'CREATE', "Menambahkan data master Bidang baru: {$namaBidang}");

        return redirect()->to('/bidang')->with('success', 'Data Bidang berhasil ditambahkan.');
    }

    /**
     * Sinkronisasi Master Bidang langsung dari API SIMPEG/SSO Pemkab Buleleng (`POST /bidang/sync-api`)
     */
    public function syncApi()
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(180);
        }
        $session   = session();
        $nama_role = $session->get('nama_role');
        $targetOpd = ($nama_role === 'Admin_OPD')
            ? (string) $session->get('id_opd')
            : trim((string) ($this->request->getPost('id_opd') ?? ''));

        try {
            $syncService = new \App\Services\SimpegSyncService();
            $stats = $syncService->syncBidang($targetOpd !== '' ? $targetOpd : null);

            $msg = sprintf(
                'Sinkronisasi Master Bidang dari API berhasil: %d OPD diproses, %d bidang dari API (%d baru, %d diperbarui).',
                $stats['opds_processed'],
                $stats['total_api'],
                $stats['inserted'],
                $stats['updated']
            );
            log_activity('Bidang', 'SYNC_API', $msg);

            if ($this->wantsJson() || $this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => $msg,
                    'data'    => $stats,
                ]);
            }

            return redirect()->to('/bidang')->with('success', $msg);
        } catch (\Throwable $e) {
            if ($this->wantsJson() || $this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => $e->getMessage(),
                ])->setStatusCode(500);
            }
            return redirect()->to('/bidang')->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $data['bidang'] = $this->bidangModel->find($id);

        if (!$data['bidang']) {
            return redirect()->to('/bidang')->with('error', 'Data Bidang tidak ditemukan.');
        }

        $session = session();
        $nama_role = $session->get('nama_role');
        $id_opd  = $session->get('id_opd');

        // Proteksi agar Admin_OPD tidak mengedit bidang di OPD lain
        if ($nama_role === 'Admin_OPD' && $data['bidang']['id_opd'] != $id_opd) {
            return redirect()->to('/bidang')->with('error', 'Akses ditolak.');
        }

        if ($nama_role === 'Admin_OPD') {
            $data['opds'] = $this->opdModel->where('id_opd', $id_opd)->findAll();
            $data['id_opd_locked'] = $id_opd;
        } else {
            $data['opds'] = $this->opdModel->findAll();
            $data['id_opd_locked'] = null;
        }

        return view('bidang/edit', $data);
    }

    public function update($id)
    {
        // Pengecekan sekuriti
        $bidangLama = $this->bidangModel->find($id);
        if (!$bidangLama) {
            return redirect()->to('/bidang')->with('error', 'Data Bidang tidak ditemukan.');
        }

        $session = session();
        $nama_role = $session->get('nama_role');
        $id_opd  = $session->get('id_opd');

        if ($nama_role === 'Admin_OPD' && $bidangLama['id_opd'] != $id_opd) {
             return redirect()->to('/bidang')->with('error', 'Akses ditolak.');
        }

        $selectedOpd = ($nama_role === 'Admin_OPD') ? $id_opd : $this->request->getPost('id_opd');
        $namaBidang = trim((string) $this->request->getPost('nama_bidang'));

        $data = [
            'id_bidang'   => $id,
            'id_opd'      => $selectedOpd,
            'nama_bidang' => $namaBidang
        ];

        if (!$this->bidangModel->save($data)) {
            $errors = $this->bidangModel->errors();
            $errMsg = !empty($errors) ? implode(' ', $errors) : 'Gagal memperbarui data Bidang.';
            return redirect()->back()->withInput()->with('error', $errMsg);
        }

        log_activity('Bidang', 'UPDATE', "Memperbarui data master Bidang: {$namaBidang}", $bidangLama, $data);

        return redirect()->to('/bidang')->with('success', 'Data Bidang berhasil diperbarui.');
    }

    public function delete($id)
    {
        $bidangLama = $this->bidangModel->find($id);
        if (!$bidangLama) {
            return redirect()->to('/bidang')->with('error', 'Data Bidang tidak ditemukan.');
        }

        $session = session();
        $nama_role = $session->get('nama_role');
        $id_opd  = $session->get('id_opd');

        if ($nama_role === 'Admin_OPD' && $bidangLama['id_opd'] != $id_opd) {
             return redirect()->to('/bidang')->with('error', 'Akses ditolak.');
        }

        try {
            $this->bidangModel->delete($id);
            log_activity('Bidang', 'DELETE', "Menghapus data master Bidang: {$bidangLama['nama_bidang']}");
            return redirect()->to('/bidang')->with('success', 'Data Bidang berhasil dihapus.');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/bidang')->with('error', 'Gagal menghapus Bidang. Pastikan Bidang ini tidak memiliki relasi data (seperti Arsip atau User).');
        }
    }
}
