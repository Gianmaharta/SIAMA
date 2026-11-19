<?php

namespace App\Services;

use Config\Database;
use Exception;

class DataCleanseService
{
    /**
     * Tabel-tabel transaksi dan operasional yang WAJIB dibersihkan.
     */
    public const TRANSACTION_TABLES = [
        'berita_acara_detail',
        'berita_acara',
        'spt_assignments',
        'spt_pelaksana',
        'notifications',
        'activity_log',
        'access_log',
        'arsip',
        'spt',
    ];

    /**
     * Tabel-tabel master data dan konfigurasi yang WAJIB dipertahankan.
     */
    public const MASTER_TABLES = [
        'opd',
        'bidang',
        'roles',
        'kode_klasifikasi',
        'jra',
        'migrations',
        'users',
        'user_roles',
    ];

    protected $db;
    protected string $arsipPath;
    protected string $sptPath;
    protected string $uploadsBasePath;

    public function __construct($db = null, ?string $arsipPath = null, ?string $sptPath = null)
    {
        $this->db = $db ?? Database::connect();

        $isTesting = (defined('ENVIRONMENT') && ENVIRONMENT === 'testing');
        if ($isTesting) {
            $base = WRITEPATH . 'testing/uploads';
            $this->uploadsBasePath = $base;
            $this->arsipPath = $arsipPath ?? ($base . '/arsip');
            $this->sptPath   = $sptPath ?? ($base . '/spt');
        } else {
            $this->uploadsBasePath = FCPATH . 'uploads';
            $this->arsipPath = $arsipPath ?? (FCPATH . 'uploads/arsip');
            $this->sptPath   = $sptPath ?? (FCPATH . 'uploads/spt');
        }

        if (!is_dir($this->arsipPath)) {
            @mkdir($this->arsipPath, 0755, true);
        }
        if (!is_dir($this->sptPath)) {
            @mkdir($this->sptPath, 0755, true);
        }
    }

    /**
     * Mendapatkan ringkasan data transaksi dan master sebelum proses reset.
     */
    public function getSummary(): array
    {
        $transactionSummary = [];
        $totalTransactionRows = 0;
        foreach (self::TRANSACTION_TABLES as $table) {
            $count = $this->db->table($table)->countAllResults();
            $transactionSummary[$table] = $count;
            $totalTransactionRows += $count;
        }

        $masterSummary = [];
        foreach (self::MASTER_TABLES as $table) {
            $masterSummary[$table] = $this->db->table($table)->countAllResults();
        }

        $filesSummary = $this->getUploadFilesSummary();

        return [
            'transactions'       => $transactionSummary,
            'total_transactions' => $totalTransactionRows,
            'masters'            => $masterSummary,
            'files'              => $filesSummary,
        ];
    }

    /**
     * Menghitung dan mendaftar file upload yang akan dibersihkan.
     */
    public function getUploadFilesSummary(): array
    {
        $arsipFiles = $this->getDirectoryFiles($this->arsipPath);
        $sptFiles   = $this->getDirectoryFiles($this->sptPath);

        return [
            'arsip_count' => count($arsipFiles),
            'arsip_files' => $arsipFiles,
            'spt_count'   => count($sptFiles),
            'spt_files'   => $sptFiles,
            'total_files' => count($arsipFiles) + count($sptFiles),
        ];
    }

    /**
     * Mengambil daftar file yang bukan index.html atau .gitkeep
     */
    protected function getDirectoryFiles(string $dir): array
    {
        if (!is_dir($dir)) {
            return [];
        }

        $items = scandir($dir);
        $files = [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..' || $item === 'index.html' || $item === '.gitkeep') {
                continue;
            }
            $full = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_file($full)) {
                $files[] = [
                    'name' => $item,
                    'path' => $full,
                    'size' => filesize($full),
                ];
            }
        }
        return $files;
    }

    /**
     * Membuat backup database dan file storage sebelum penghapusan data.
     */
    public function createBackup(?string $targetDir = null): array
    {
        $timestamp = date('Ymd_His');
        if (empty($targetDir)) {
            $targetDir = ROOTPATH . 'backups/backup_' . $timestamp;
        }

        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                throw new Exception("Gagal membuat direktori backup: {$targetDir}");
            }
        }

        $dbDumpFile = $targetDir . DIRECTORY_SEPARATOR . 'db_siama.sql';
        $this->dumpDatabase($dbDumpFile);

        if (!file_exists($dbDumpFile) || filesize($dbDumpFile) === 0) {
            throw new Exception("File backup database gagal dibuat atau berukuran 0 byte: {$dbDumpFile}");
        }

        $storageBackupDir = $targetDir . DIRECTORY_SEPARATOR . 'uploads';
        $this->copyDirectory($this->uploadsBasePath, $storageBackupDir);

        return [
            'backup_dir'   => $targetDir,
            'db_file'      => $dbDumpFile,
            'db_size'      => filesize($dbDumpFile),
            'storage_dir'  => $storageBackupDir,
            'timestamp'    => $timestamp,
        ];
    }

    /**
     * Dump database menggunakan mysqldump atau fallback PHP PDO export.
     */
    protected function dumpDatabase(string $outputFile): void
    {
        $dbConfig = config('Database')->default;
        $host     = $dbConfig['hostname'] ?? 'localhost';
        $user     = $dbConfig['username'] ?? 'root';
        $pass     = $dbConfig['password'] ?? '';
        $database = $dbConfig['database'] ?? 'db_siama';
        $port     = $dbConfig['port'] ?? 3306;

        // Coba gunakan mysqldump jika tersedia di server
        $passPart = !empty($pass) ? ' -p' . escapeshellarg($pass) : '';
        $cmd = sprintf(
            'mysqldump -h %s -P %s -u %s%s %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg((string) $port),
            escapeshellarg($user),
            $passPart,
            escapeshellarg($database),
            escapeshellarg($outputFile)
        );

        $output = [];
        $returnVar = 0;
        @exec($cmd, $output, $returnVar);

        if ($returnVar === 0 && file_exists($outputFile) && filesize($outputFile) > 100) {
            return;
        }

        // Fallback: Export database secara native via PDO
        $this->exportDatabaseNative($outputFile);
    }

    /**
     * Native PHP Export Database as SQL dump
     */
    protected function exportDatabaseNative(string $outputFile): void
    {
        $handle = fopen($outputFile, 'w');
        if (!$handle) {
            throw new Exception("Tidak dapat membuka file backup untuk penulisan: {$outputFile}");
        }

        fwrite($handle, "-- SIAMA Database Backup\n-- Generated: " . date('Y-m-d H:i:s') . "\n\nSET FOREIGN_KEY_CHECKS=0;\n\n");

        $tables = $this->db->listTables();
        foreach ($tables as $table) {
            $createTableQuery = $this->db->query("SHOW CREATE TABLE `{$table}`")->getRowArray();
            $createSql = $createTableQuery['Create Table'] ?? '';
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n" . $createSql . ";\n\n");

            $rows = $this->db->table($table)->get()->getResultArray();
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $cols = array_map(function ($c) {
                        return "`" . str_replace("`", "``", $c) . "`";
                    }, array_keys($row));

                    $vals = array_map(function ($v) {
                        if ($v === null) return 'NULL';
                        return $this->db->escape($v);
                    }, array_values($row));

                    fwrite($handle, "INSERT INTO `{$table}` (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $vals) . ");\n");
                }
                fwrite($handle, "\n");
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
    }

    /**
     * Salin seluruh isi direktori secara rekursif
     */
    protected function copyDirectory(string $src, string $dst): void
    {
        if (!is_dir($src)) return;
        if (!is_dir($dst)) {
            mkdir($dst, 0755, true);
        }

        $dir = opendir($src);
        while (($file = readdir($dir)) !== false) {
            if ($file !== '.' && $file !== '..') {
                $srcFile = $src . DIRECTORY_SEPARATOR . $file;
                $dstFile = $dst . DIRECTORY_SEPARATOR . $file;
                if (is_dir($srcFile)) {
                    $this->copyDirectory($srcFile, $dstFile);
                } else {
                    copy($srcFile, $dstFile);
                }
            }
        }
        closedir($dir);
    }

    /**
     * Eksekusi proses reset data (data cleansing)
     */
    public function executeCleanse(bool $skipBackup = false): array
    {
        // 1. Ketentuan Keamanan: Lakukan backup terlebih dahulu
        $backupResult = null;
        if (!$skipBackup) {
            $backupResult = $this->createBackup();
        }

        // 2. Penghapusan data transaksi dalam urutan dependensi aman
        $this->db->transStart();

        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        $clearedCounts = [];
        foreach (self::TRANSACTION_TABLES as $table) {
            $count = $this->db->table($table)->countAllResults();
            $this->db->table($table)->emptyTable();
            $clearedCounts[$table] = $count;
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new Exception("Gagal menjalankan reset database: Transaksi database dibatalkan (rollback).");
        }

        // 3. Pembersihan File Storage (arsip & spt)
        $deletedFiles = $this->cleanUploadFiles();

        // 4. Validasi Hasil Reset
        $validation = $this->validateCleanState();

        return [
            'status'        => 'success',
            'backup'        => $backupResult,
            'cleared_rows'  => $clearedCounts,
            'deleted_files' => $deletedFiles,
            'validation'    => $validation,
        ];
    }

    /**
     * Hapus file upload pada folder arsip dan spt (kecuali index.html dan .gitkeep)
     */
    protected function cleanUploadFiles(): array
    {
        $deleted = ['arsip' => [], 'spt' => []];

        $arsipFiles = $this->getDirectoryFiles($this->arsipPath);
        foreach ($arsipFiles as $f) {
            if (unlink($f['path'])) {
                $deleted['arsip'][] = $f['name'];
            }
        }

        $sptFiles = $this->getDirectoryFiles($this->sptPath);
        foreach ($sptFiles as $f) {
            if (unlink($f['path'])) {
                $deleted['spt'][] = $f['name'];
            }
        }

        return $deleted;
    }

    /**
     * Validasi menyeluruh bahwa database dan storage dalam kondisi bersih
     */
    public function validateCleanState(): array
    {
        $checks = [];

        // 1. Pastikan seluruh tabel transaksi bernilai 0
        $allTransactionsZero = true;
        foreach (self::TRANSACTION_TABLES as $table) {
            $c = $this->db->table($table)->countAllResults();
            $checks['transactions'][$table] = [
                'count' => $c,
                'is_clean' => ($c === 0),
            ];
            if ($c !== 0) {
                $allTransactionsZero = false;
            }
        }

        // 2. Pastikan tabel master tetap utuh
        $mastersIntact = true;
        foreach (self::MASTER_TABLES as $table) {
            $c = $this->db->table($table)->countAllResults();
            $checks['masters'][$table] = [
                'count' => $c,
                'has_data' => ($c > 0 || $table === 'jra'), // jra awal bisa 0
            ];
            if ($table !== 'jra' && $c === 0) {
                $mastersIntact = false;
            }
        }

        // 3. Pastikan Akun Admin Pemkab Utama tetap aktif
        $adminUser = $this->db->table('users')
                              ->select('users.*, roles.nama_role')
                              ->join('user_roles', 'user_roles.id_user = users.id_user')
                              ->join('roles', 'roles.id_role = user_roles.id_role')
                              ->where('users.email', 'admin.pemkab@siama.test')
                              ->get()
                              ->getRowArray();

        $adminIntact = ($adminUser !== null && $adminUser['nama_role'] === 'Admin_Pemkab' && (int)$adminUser['is_active'] === 1);
        $checks['admin_pemkab'] = [
            'exists'     => ($adminUser !== null),
            'email'      => $adminUser['email'] ?? null,
            'role'       => $adminUser['nama_role'] ?? null,
            'is_active'  => $adminUser['is_active'] ?? null,
            'is_valid'   => $adminIntact,
        ];

        // 4. Pastikan file upload bersih
        $filesSummary = $this->getUploadFilesSummary();
        $filesClean = ($filesSummary['total_files'] === 0);
        $checks['files'] = [
            'remaining_files' => $filesSummary['total_files'],
            'is_clean'        => $filesClean,
        ];

        $allValid = $allTransactionsZero && $mastersIntact && $adminIntact && $filesClean;

        return [
            'is_all_valid' => $allValid,
            'details'      => $checks,
        ];
    }
}
