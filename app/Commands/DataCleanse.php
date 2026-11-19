<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\DataCleanseService;
use Exception;

class DataCleanse extends BaseCommand
{
    protected $group       = 'SIAMA';
    protected $name        = 'data:cleanse';
    protected $description = 'Reset seluruh data transaksi dan operasional SIAMEDA ke kondisi awal (data cleansing).';
    protected $usage       = 'data:cleanse [options]';
    protected $options     = [
        '--dry-run'     => 'Menampilkan ringkasan data yang akan dihapus tanpa melakukan perubahan.',
        '--force'       => 'Menjalankan reset secara langsung tanpa meminta konfirmasi.',
        '--skip-backup' => 'Melewati tahap backup otomatis (TIDAK DIREKOMENDASIKAN).',
    ];

    public function run(array $params)
    {
        $service = new DataCleanseService();

        CLI::write("=======================================================", 'yellow');
        CLI::write("    SIAMEDA - DATA CLEANSING & OPERATIONAL RESET       ", 'yellow');
        CLI::write("=======================================================", 'yellow');
        CLI::newLine();

        // 1. Ambil & Tampilkan Ringkasan
        $summary = $service->getSummary();

        CLI::write("1. TABEL TRANSAKSI & OPERASIONAL YANG AKAN DIKOSONGKAN:", 'cyan');
        foreach ($summary['transactions'] as $tbl => $count) {
            CLI::write(sprintf("   - %-25s : %d baris", $tbl, $count), $count > 0 ? 'red' : 'white');
        }
        CLI::write(sprintf("   TOTAL DATA TRANSAKSI   : %d baris", $summary['total_transactions']), 'yellow');
        CLI::newLine();

        CLI::write("2. FILE STORAGE UPLOAD YANG AKAN DIBERSIHKAN:", 'cyan');
        CLI::write(sprintf("   - Uploads Arsip          : %d file", $summary['files']['arsip_count']), 'white');
        CLI::write(sprintf("   - Uploads SPT            : %d file", $summary['files']['spt_count']), 'white');
        CLI::write(sprintf("   TOTAL FILE               : %d file", $summary['files']['total_files']), 'yellow');
        CLI::newLine();

        CLI::write("3. DATA MASTER & AKUN UTAMA YANG AKAN DIPERTAHANKAN:", 'green');
        foreach ($summary['masters'] as $tbl => $count) {
            CLI::write(sprintf("   + %-25s : %d baris (DIJAMIN UTUH)", $tbl, $count), 'green');
        }
        CLI::newLine();

        $isDryRun = array_key_exists('dry-run', $params) || CLI::getOption('dry-run');
        if ($isDryRun) {
            CLI::write("[DRY-RUN] Mode simulasi selesai. Tidak ada data yang diubah.", 'yellow');
            return;
        }

        $isForce = array_key_exists('force', $params) || CLI::getOption('force');
        if (!$isForce) {
            $confirm = CLI::prompt('Apakah Anda yakin ingin melakukan reset data transaksi sekarang? (y/n)', ['y', 'n']);
            if (strtolower($confirm) !== 'y') {
                CLI::write('Operasi dibatalkan oleh pengguna.', 'red');
                return;
            }
        }

        $skipBackup = array_key_exists('skip-backup', $params) || CLI::getOption('skip-backup');

        try {
            if (!$skipBackup) {
                CLI::write("Membuat backup database dan file storage...", 'yellow');
            }

            $result = $service->executeCleanse((bool) $skipBackup);

            if ($result['backup']) {
                CLI::write("Backup database berhasil dibuat : " . $result['backup']['db_file'] . " (" . round($result['backup']['db_size'] / 1024, 2) . " KB)", 'green');
                CLI::write("Backup file storage berhasil    : " . $result['backup']['storage_dir'], 'green');
            }

            CLI::newLine();
            CLI::write("Hasil Pembersihan Data:", 'cyan');
            foreach ($result['cleared_rows'] as $tbl => $c) {
                CLI::write(sprintf("   - %-25s : %d baris dihapus", $tbl, $c), 'white');
            }
            CLI::write(sprintf("   - File arsip terhapus    : %d file", count($result['deleted_files']['arsip'])), 'white');
            CLI::write(sprintf("   - File SPT terhapus      : %d file", count($result['deleted_files']['spt'])), 'white');

            CLI::newLine();
            CLI::write("Validasi Status Bersih:", 'cyan');
            $val = $result['validation'];
            if ($val['is_all_valid']) {
                CLI::write("   [V] Semua tabel transaksi bernilai 0.", 'green');
                CLI::write("   [V] Seluruh tabel master data tetap utuh.", 'green');
                CLI::write("   [V] Akun Admin Pemkab utama (admin.pemkab@siama.test) aktif dan utuh.", 'green');
                CLI::write("   [V] File storage upload bersih.", 'green');
                CLI::newLine();
                CLI::write("SUKSES: Sistem SIAMEDA telah kembali dalam kondisi bersih seperti instalasi baru!", 'green');
            } else {
                CLI::write("PERINGATAN: Terdapat item yang belum sepenuhnya bersih. Silakan cek rincian validasi.", 'red');
            }
        } catch (Exception $e) {
            CLI::error("TERJADI KESALAHAN: " . $e->getMessage());
        }
    }
}
