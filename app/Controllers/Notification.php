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

        // Catatan: Notifikasi TIDAK langsung ditandai dibaca di sini.
        // Notifikasi hanya ditandai dibaca setelah user mengambil aksi (di method action()).
        // Ini memastikan badge notifikasi tetap aktif sampai user benar-benar menindaklanjuti.

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

        // Guard: Cegah perubahan pada arsip yang sudah dalam status final
        if (in_array($arsip['status_retensi_aktif'], ['Musnah', 'Permanen'])) {
            $this->notifModel->delete($id_notif); // Hapus notifikasi basi
            return redirect()->back()->with('error', 'Arsip ini sudah dalam status final (Musnah/Permanen) dan tidak dapat diubah lagi.');
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

        // Tandai notifikasi sebagai dibaca dan hapus setelah ditindaklanjuti
        $this->notifModel->update($id_notif, ['is_read' => 1]);
        $this->notifModel->delete($id_notif);

        return redirect()->back()->with('success', $msg);
    }
}
