<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use App\Models\ArsipModel;
use App\Models\BeritaAcaraModel; // if we want to create a log of destruction

class Notification extends BaseController
{
    protected $notifModel;
    protected $arsipModel;

    public function __construct()
    {
        $this->notifModel = new NotificationModel();
        $this->arsipModel = new ArsipModel();
    }

    public function index()
    {
        $session = session();
        $user_id = $session->get('user_id');

        $data['title'] = 'Notifikasi Sistem';
        $data['notifications'] = $this->notifModel->getByUser($user_id);

        // Tandai semua sebagai dibaca saat dibuka
        $this->notifModel->where('id_user', $user_id)->set(['is_read' => 1])->update();

        return view('notification/index', $data);
    }

    public function action($id_notif)
    {
        $session = session();
        if ($session->get('nama_role') !== 'Admin_OPD') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $notif = $this->notifModel->find($id_notif);
        if (!$notif || !in_array($notif['action_type'], ['retensi_arsip', 'retensi_inaktif_arsip'])) {
            return redirect()->back()->with('error', 'Notifikasi tidak valid.');
        }

        $action = $this->request->getPost('action');
        $id_arsip = $notif['action_id'];

        $arsip = $this->arsipModel->find($id_arsip);
        if (!$arsip) {
            return redirect()->back()->with('error', 'Arsip tidak ditemukan.');
        }

        if ($action === 'musnahkan') {
            // Soft delete/Ubah status saja tanpa hapus file sesuai permintaan user
            $this->arsipModel->update($id_arsip, ['status_retensi_aktif' => 'Musnah']);
            $msg = 'Arsip berhasil ditetapkan sebagai Musnah (Soft Delete).';
        } elseif ($action === 'inaktif') {
            $db = \Config\Database::connect();
            $jra = $db->table('jra')->where('id_kode_klasifikasi', $arsip['id_kode_klasifikasi'])->get()->getRowArray();
            $retensi_inaktif = $jra ? (int)$jra['retensi_inaktif'] : 0;
            $tanggal_inaktif = date('Y-m-d', strtotime("+$retensi_inaktif years"));

            $this->arsipModel->update($id_arsip, [
                'status_retensi_aktif' => 'Inaktif',
                'tanggal_retensi_inaktif_berakhir' => $tanggal_inaktif
            ]);
            $msg = 'Arsip berhasil dipindahkan ke Inaktif dan jadwal retensi inaktif telah diatur.';
        } elseif ($action === 'permanen') {
            $this->arsipModel->update($id_arsip, ['status_retensi_aktif' => 'Permanen']);
            $msg = 'Arsip berhasil ditetapkan sebagai arsip Permanen.';
        } elseif ($action === 'perpanjang') {
            // Perpanjang 1 tahun
            $newDate = date('Y-m-d', strtotime('+1 year', strtotime($arsip['tanggal_retensi_aktif_berakhir'])));
            $this->arsipModel->update($id_arsip, [
                'status_retensi_aktif' => 'Aktif',
                'tanggal_retensi_aktif_berakhir' => $newDate
            ]);
            $msg = 'Retensi Aktif arsip berhasil diperpanjang 1 tahun.';
        } else {
            return redirect()->back()->with('error', 'Aksi tidak dikenal.');
        }

        // Hapus notifikasi setelah ditindaklanjuti
        $this->notifModel->delete($id_notif);

        return redirect()->back()->with('success', $msg);
    }
}
