<?php

namespace App\Controllers;

use App\Services\DataCleanseService;
use CodeIgniter\HTTP\ResponseInterface;

class SystemMaintenance extends BaseController
{
    protected $cleanseService;

    public function __construct()
    {
        $this->cleanseService = new DataCleanseService();
    }

    /**
     * Halaman ringkasan status data cleansing (Khusus Admin Pemkab)
     */
    public function index()
    {
        $session = session();
        $role = $session->get('nama_role');

        if (!in_array($role, ['Super_Admin', 'Admin_Pemkab'], true)) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak. Fitur Data Cleansing hanya dapat diakses oleh Admin Pemkab utama.');
        }

        $summary = $this->cleanseService->getSummary();
        $validation = $this->cleanseService->validateCleanState();

        $data = [
            'title'      => 'Reset & Data Cleansing - SIAMA',
            'summary'    => $summary,
            'validation' => $validation,
        ];

        return view('system/cleanse', $data);
    }

    /**
     * Eksekusi proses reset data melalui Web Interface (Khusus Admin Pemkab)
     */
    public function execute(): ResponseInterface
    {
        $session = session();
        $role = $session->get('nama_role');

        if (!in_array($role, ['Super_Admin', 'Admin_Pemkab'], true)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Akses ditolak. Hanya Admin Pemkab utama yang berwenang melakukan data cleansing.'
            ])->setStatusCode(403);
        }

        try {
            $result = $this->cleanseService->executeCleanse(false);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Data cleansing berhasil dilaksanakan. Seluruh data transaksi telah dibersihkan dan backup tersimpan.',
                    'data'    => $result,
                ]);
            }

            return redirect()->to('/system-cleanse')->with('success', 'Data cleansing berhasil dilaksanakan. Seluruh data transaksi telah bersih dan backup telah disimpan.');
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => $e->getMessage(),
                ])->setStatusCode(500);
            }

            return redirect()->to('/system-cleanse')->with('error', 'Gagal melakukan reset: ' . $e->getMessage());
        }
    }
}
