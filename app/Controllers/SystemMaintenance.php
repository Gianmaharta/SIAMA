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
        helper('logger');
    }

    /**
     * Halaman ringkasan status data cleansing & Backup Database
     */
    public function index()
    {
        $session = session();
        $role = $session->get('nama_role');

        if (!in_array($role, ['Super_Admin', 'Admin_Pemkab'], true)) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak. Fitur Data Cleansing hanya dapat diakses oleh Admin Pemkab utama.');
        }

        $summary    = $this->cleanseService->getSummary();
        $validation = $this->cleanseService->validateCleanState();
        $backups    = ($role === 'Super_Admin') ? $this->cleanseService->listBackups() : [];

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status'     => 'success',
                'summary'    => $summary,
                'validation' => $validation,
                'backups'    => $backups,
            ]);
        }

        $data = [
            'title'      => 'Reset & Data Cleansing - SIAMA',
            'summary'    => $summary,
            'validation' => $validation,
            'backups'    => $backups,
        ];

        return view('system/cleanse', $data);
    }

    /**
     * Membuat backup database .sql mandiri tanpa menghapus data (Khusus Super_Admin)
     */
    public function createBackup(): ResponseInterface
    {
        if (session()->get('nama_role') !== 'Super_Admin') {
            if ($this->wantsJson() || $this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Akses ditolak. Fitur Backup Database hanya untuk Super Admin.',
                ])->setStatusCode(403);
            }
            return redirect()->to('/system-cleanse')->with('error', 'Akses ditolak. Fitur Backup Database hanya untuk Super Admin.');
        }

        try {
            $result = $this->cleanseService->createDatabaseOnlyBackup();
            log_activity('System', 'BACKUP', 'Membuat backup database manual: ' . ($result['folder'] ?? ''));

            if ($this->wantsJson() || $this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Backup database (.sql) berhasil dibuat tanpa menghapus data.',
                    'data'    => $result,
                ]);
            }

            return redirect()->to('/system-cleanse')->with('success', 'Backup database (.sql) berhasil dibuat: ' . $result['folder']);
        } catch (\Throwable $e) {
            if ($this->wantsJson() || $this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal membuat backup database: ' . $e->getMessage(),
                ])->setStatusCode(500);
            }
            return redirect()->to('/system-cleanse')->with('error', 'Gagal membuat backup database: ' . $e->getMessage());
        }
    }

    /**
     * Mengunduh file backup .sql (Khusus Super_Admin)
     */
    public function downloadBackup(string $folder)
    {
        if (session()->get('nama_role') !== 'Super_Admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $filePath = $this->cleanseService->getBackupFilePath($folder);
        if (!$filePath) {
            return redirect()->to('/system-cleanse')->with('error', 'File backup tidak ditemukan.');
        }

        $downloadName = str_replace('backup_', 'siama_backup_', $folder) . '.sql';
        log_activity('System', 'DOWNLOAD_BACKUP', 'Mengunduh file backup database: ' . $downloadName);

        return $this->response->download($filePath, null)->setFileName($downloadName);
    }

    /**
     * Menghapus folder backup tertentu (Khusus Super_Admin)
     */
    public function deleteBackup(string $folder): ResponseInterface
    {
        if (session()->get('nama_role') !== 'Super_Admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        if (!$this->cleanseService->deleteBackup($folder)) {
            return redirect()->to('/system-cleanse')->with('error', 'Gagal menghapus backup atau folder tidak ditemukan.');
        }

        log_activity('System', 'DELETE_BACKUP', 'Menghapus file backup database: ' . $folder);

        if ($this->wantsJson() || $this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'File backup berhasil dihapus.',
            ]);
        }

        return redirect()->to('/system-cleanse')->with('success', 'File backup ' . $folder . ' berhasil dihapus.');
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
