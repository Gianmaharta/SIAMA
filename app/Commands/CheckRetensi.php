<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ArsipModel;
use App\Models\NotificationModel;
use App\Models\UserModel;

class CheckRetensi extends BaseCommand
{
    protected $group       = 'SIAMA';
    protected $name        = 'check:retensi';
    protected $description = 'Mengecek arsip yang telah melewati masa retensi aktif dan mengirim notifikasi.';

    public function run(array $params)
    {
        $arsipModel = new ArsipModel();
        $notifModel = new NotificationModel();
        $userModel  = new UserModel();

        CLI::write('Mulai mengecek jadwal retensi arsip...', 'yellow');

        $today = date('Y-m-d');
        
        // Cari arsip yang tanggal retensi aktif berakhir <= hari ini dan statusnya masih Aktif
        $expiredArsip = $arsipModel->where('tanggal_retensi_aktif_berakhir <=', $today)
                                   ->where('tanggal_retensi_aktif_berakhir !=', null)
                                   ->where('status_retensi_aktif', 'Aktif')
                                   ->findAll();

        if (empty($expiredArsip)) {
            CLI::write('Tidak ada arsip AKTIF yang melewati masa retensi hari ini.', 'green');
        } else {
            CLI::write('Ditemukan ' . count($expiredArsip) . ' arsip aktif yang kedaluwarsa. Memproses notifikasi...', 'yellow');
        }

        $count = 0;
        foreach ($expiredArsip as $arsip) {
            // Cari Admin OPD dari OPD arsip tersebut (Role ID 4 = Admin_OPD)
            $adminOpd = $userModel->select('users.*')
                                  ->join('user_roles', 'user_roles.id_user = users.id_user')
                                  ->join('roles', 'roles.id_role = user_roles.id_role')
                                  ->where('users.id_opd', $arsip['id_opd'])
                                  ->where('roles.nama_role', 'Admin_OPD')
                                  ->findAll();

            foreach ($adminOpd as $admin) {
                // Cek apakah notifikasi sudah pernah dikirim untuk arsip ini agar tidak dobel
                $exists = $notifModel->where('id_user', $admin['id_user'])
                                     ->where('action_id', $arsip['id_arsip'])
                                     ->where('action_type', 'retensi_arsip')
                                     ->first();
                if (!$exists) {
                    $notifModel->insert([
                        'id_user'     => $admin['id_user'],
                        'title'       => 'Peringatan Retensi Arsip',
                        'message'     => 'Arsip "' . $arsip['nama_arsip'] . '" (' . $arsip['nomor_arsip'] . ') telah melewati masa retensi aktif.',
                        'action_type' => 'retensi_arsip',
                        'action_id'   => $arsip['id_arsip'],
                    ]);
                    $count++;
                }
            }
        }
        
        // Cari arsip inaktif yang tanggal retensi inaktifnya berakhir <= hari ini
        $expiredInaktif = $arsipModel->where('tanggal_retensi_inaktif_berakhir <=', $today)
                                   ->where('tanggal_retensi_inaktif_berakhir !=', null)
                                   ->where('status_retensi_aktif', 'Inaktif')
                                   ->findAll();

        if (empty($expiredInaktif)) {
            CLI::write('Tidak ada arsip INAKTIF yang melewati masa retensi inaktif hari ini.', 'green');
        } else {
            CLI::write('Ditemukan ' . count($expiredInaktif) . ' arsip inaktif yang kedaluwarsa. Memproses notifikasi...', 'yellow');
        }

        foreach ($expiredInaktif as $arsip) {
            $adminOpd = $userModel->select('users.*')
                                  ->join('user_roles', 'user_roles.id_user = users.id_user')
                                  ->join('roles', 'roles.id_role = user_roles.id_role')
                                  ->where('users.id_opd', $arsip['id_opd'])
                                  ->where('roles.nama_role', 'Admin_OPD')
                                  ->findAll();

            foreach ($adminOpd as $admin) {
                $exists = $notifModel->where('id_user', $admin['id_user'])
                                     ->where('action_id', $arsip['id_arsip'])
                                     ->where('action_type', 'retensi_inaktif_arsip')
                                     ->first();
                if (!$exists) {
                    $notifModel->insert([
                        'id_user'     => $admin['id_user'],
                        'title'       => 'Peringatan Retensi Inaktif Habis',
                        'message'     => 'Masa inaktif untuk Arsip "' . $arsip['nama_arsip'] . '" (' . $arsip['nomor_arsip'] . ') telah habis. Lakukan aksi akhir.',
                        'action_type' => 'retensi_inaktif_arsip',
                        'action_id'   => $arsip['id_arsip'],
                    ]);
                    $count++;
                }
            }
        }

        CLI::write("Selesai! $count notifikasi baru berhasil dikirim.", 'green');
    }
}
