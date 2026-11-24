<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\NotificationService;

class CheckRetensi extends BaseCommand
{
    protected $group       = 'SIAMA';
    protected $name        = 'check:retensi';
    protected $description = 'Mengecek arsip yang telah melewati masa retensi aktif dan mengirim notifikasi.';

    public function run(array $params)
    {
        CLI::write('Mulai mengecek jadwal retensi arsip...', 'yellow');

        $service = new NotificationService();
        $result  = $service->checkAndNotifyExpiredRetention();

        if ($result['expired_aktif'] === 0) {
            CLI::write('Tidak ada arsip AKTIF yang melewati masa retensi hari ini.', 'green');
        } else {
            CLI::write('Ditemukan ' . $result['expired_aktif'] . ' arsip aktif yang kedaluwarsa. Memproses notifikasi...', 'yellow');
        }

        if ($result['expired_inaktif'] === 0) {
            CLI::write('Tidak ada arsip INAKTIF yang melewati masa retensi inaktif hari ini.', 'green');
        } else {
            CLI::write('Ditemukan ' . $result['expired_inaktif'] . ' arsip inaktif yang kedaluwarsa. Memproses notifikasi...', 'yellow');
        }

        CLI::write("Selesai! {$result['notifications_sent']} notifikasi baru berhasil dikirim.", 'green');
    }
}
