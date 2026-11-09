<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use App\Models\ArsipModel;
use CodeIgniter\HTTP\ResponseInterface;

class Notification extends BaseController
{
    protected $notifModel;
    protected $arsipModel;

    public function __construct()
    {
        $this->notifModel = new NotificationModel();
        $this->arsipModel = new ArsipModel();
    }

    /**
     * Halaman Utama Notifikasi
     */
    public function index()
    {
        $session = session();
        $userId  = $session->get('user_id');

        if (empty($userId)) {
            return redirect()->to('/login');
        }

        $filter = $this->request->getGet('filter') ?: 'all';
        if (!in_array($filter, ['all', 'unread', 'read'])) {
            $filter = 'all';
        }

        $notifications = $this->notifModel->getByUser($userId, $filter);

        // Tambahkan metadata ikon dan waktu relatif
        foreach ($notifications as &$notif) {
            $meta = $this->getNotificationMeta($notif['type'] ?: $notif['action_type']);
            $notif['meta']      = $meta;
            $notif['time_ago']  = $this->formatTimeAgo($notif['created_at']);
            $notif['target_url'] = $this->resolveTargetUrl($notif);
        }
        unset($notif);

        // Statistik Filter
        $totalAll    = count($this->notifModel->getByUser($userId, 'all'));
        $totalUnread = $this->notifModel->getUnreadCount($userId);
        $totalRead   = max(0, $totalAll - $totalUnread);

        $data = [
            'title'         => 'Pusat Notifikasi - SIAMA',
            'notifications' => $notifications,
            'currentFilter' => $filter,
            'totalAll'      => $totalAll,
            'totalUnread'   => $totalUnread,
            'totalRead'     => $totalRead,
        ];

        return view('notification/index', $data);
    }

    /**
     * Endpoint API: Menghitung jumlah notifikasi belum dibaca (untuk polling AJAX navbar)
     */
    public function unreadCount(): ResponseInterface
    {
        $userId = session()->get('user_id');
        if (empty($userId)) {
            return $this->response->setJSON(['status' => 'error', 'unread' => 0]);
        }

        $count = $this->notifModel->getUnreadCount($userId);

        return $this->response->setJSON([
            'status' => 'success',
            'unread' => (int) $count
        ]);
    }

    /**
     * Endpoint API: Mengambil daftar notifikasi ringkas untuk Dropdown Navbar
     */
    public function getDropdownList(): ResponseInterface
    {
        $userId = session()->get('user_id');
        if (empty($userId)) {
            return $this->response->setJSON([
                'status'        => 'error',
                'unread'        => 0,
                'notifications' => []
            ]);
        }

        $unreadCount = $this->notifModel->getUnreadCount($userId);
        $rawNotifs   = $this->notifModel->getDropdownNotifications($userId, 6);

        $formatted = [];
        foreach ($rawNotifs as $notif) {
            $meta = $this->getNotificationMeta($notif['type'] ?: $notif['action_type']);
            $formatted[] = [
                'id_notification' => $notif['id_notification'],
                'title'           => esc($notif['title']),
                'message'         => esc($notif['message']),
                'is_read'         => (int) $notif['is_read'],
                'time_ago'        => $this->formatTimeAgo($notif['created_at']),
                'type'            => $notif['type'] ?: $notif['action_type'],
                'icon'            => $meta['icon'],
                'icon_bg'         => $meta['bg'],
                'icon_color'      => $meta['color'],
                'type_label'      => $meta['label'],
                'redirect_url'    => base_url('/notification/read-and-redirect/' . $notif['id_notification'])
            ];
        }

        return $this->response->setJSON([
            'status'        => 'success',
            'unread'        => (int) $unreadCount,
            'notifications' => $formatted
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai dibaca lalu langsung redirect ke tujuan
     */
    public function readAndRedirect(string $id_notification)
    {
        $userId = session()->get('user_id');
        if (empty($userId)) {
            return redirect()->to('/login');
        }

        $notif = $this->notifModel->find($id_notification);
        if ($notif && $notif['id_user'] === $userId) {
            $this->notifModel->markAsRead($id_notification, $userId);
            $targetUrl = $this->resolveTargetUrl($notif);
            return redirect()->to($targetUrl);
        }

        return redirect()->to('/notification');
    }

    /**
     * Tandai satu notifikasi sebagai telah dibaca (AJAX/POST)
     */
    public function markRead(string $id_notification)
    {
        $userId = session()->get('user_id');
        if (empty($userId)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
            }
            return redirect()->to('/login');
        }

        $this->notifModel->markAsRead($id_notification, $userId);
        $newUnread = $this->notifModel->getUnreadCount($userId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Notifikasi ditandai dibaca.',
                'unread'  => (int) $newUnread
            ]);
        }

        return redirect()->back()->with('success', 'Notifikasi berhasil ditandai sebagai dibaca.');
    }

    /**
     * Tandai semua notifikasi user sebagai telah dibaca (AJAX/POST)
     */
    public function markAllRead()
    {
        $userId = session()->get('user_id');
        if (empty($userId)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
            }
            return redirect()->to('/login');
        }

        $this->notifModel->markAllAsRead($userId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Semua notifikasi berhasil ditandai telah dibaca.',
                'unread'  => 0
            ]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi berhasil ditandai sebagai dibaca.');
    }

    /**
     * Aksi Lanjutan Notifikasi Retensi JRA (Admin OPD)
     */
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

        $action   = $this->request->getPost('action');
        $id_arsip = $notif['action_id'] ?: $notif['reference_id'];

        $arsip = $this->arsipModel->find($id_arsip);
        if (!$arsip) {
            return redirect()->back()->with('error', 'Arsip tidak ditemukan.');
        }

        // Guard: Cegah perubahan pada arsip yang sudah dalam status final
        if (in_array($arsip['status_retensi_aktif'], ['Musnah', 'Permanen'])) {
            $this->notifModel->delete($id_notif);
            return redirect()->back()->with('error', 'Arsip ini sudah dalam status final (Musnah/Permanen) dan tidak dapat diubah lagi.');
        }

        if ($action === 'musnahkan') {
            // BR-06: Arsip hanya boleh dimusnahkan jika telah memiliki Berita Acara yang sah
            if (empty($arsip['id_berita_acara'])) {
                return redirect()->back()->with('error', 'Pemusnahan ditolak: Arsip belum memiliki Berita Acara yang sah (Aturan BR-06).');
            }
            $this->arsipModel->update($id_arsip, ['status_retensi_aktif' => 'Musnah']);
            $msg = 'Arsip berhasil ditetapkan sebagai Musnah (Soft Delete).';
        } elseif ($action === 'inaktif') {
            $db = \Config\Database::connect();
            $jra = $db->table('jra')->where('id_kode_klasifikasi', $arsip['id_kode_klasifikasi'])->get()->getRowArray();
            $retensi_inaktif = $jra ? (int)$jra['retensi_inaktif'] : 0;
            $tanggal_inaktif = date('Y-m-d', strtotime("+$retensi_inaktif years"));

            $this->arsipModel->update($id_arsip, [
                'status_retensi_aktif'             => 'Inaktif',
                'tanggal_retensi_inaktif_berakhir' => $tanggal_inaktif
            ]);
            $msg = 'Arsip berhasil dipindahkan ke Inaktif dan jadwal retensi inaktif telah diatur.';
        } elseif ($action === 'permanen') {
            $this->arsipModel->update($id_arsip, ['status_retensi_aktif' => 'Permanen']);
            $msg = 'Arsip berhasil ditetapkan sebagai arsip Permanen.';
        } elseif ($action === 'perpanjang') {
            $newDate = date('Y-m-d', strtotime('+1 year', strtotime($arsip['tanggal_retensi_aktif_berakhir'])));
            $this->arsipModel->update($id_arsip, [
                'status_retensi_aktif'           => 'Aktif',
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

    /**
     * Resolusi URL Tujuan Notifikasi
     */
    private function resolveTargetUrl(array $notif): string
    {
        if (!empty($notif['url'])) {
            return $notif['url'];
        }

        $type = $notif['type'] ?: $notif['action_type'];
        $refId = $notif['reference_id'] ?: $notif['action_id'];

        switch ($type) {
            case 'spt':
                return !empty($refId) ? base_url('/spt/detail/' . $refId) : base_url('/spt');
            case 'arsip':
                return !empty($refId) ? base_url('/arsip/detail/' . $refId) : base_url('/arsip');
            case 'berita_acara':
                return !empty($refId) ? base_url('/berita-acara/detail/' . $refId) : base_url('/berita-acara');
            case 'user':
                return base_url('/users');
            default:
                return base_url('/notification');
        }
    }

    /**
     * Metadata Tampilan Ikon & Warna Berdasarkan Jenis Notifikasi
     */
    private function getNotificationMeta(?string $type): array
    {
        switch ($type) {
            case 'spt':
                return [
                    'icon'  => 'bi-briefcase-fill',
                    'bg'    => '#EFF6FF',
                    'color' => '#1E60EC',
                    'label' => 'Penugasan SPT'
                ];
            case 'arsip':
                return [
                    'icon'  => 'bi-archive-fill',
                    'bg'    => '#FEF3C7',
                    'color' => '#D97706',
                    'label' => 'Alih Media Arsip'
                ];
            case 'berita_acara':
                return [
                    'icon'  => 'bi-file-earmark-check-fill',
                    'bg'    => '#ECFDF5',
                    'color' => '#10B981',
                    'label' => 'Berita Acara'
                ];
            case 'user':
                return [
                    'icon'  => 'bi-person-badge-fill',
                    'bg'    => '#F3E8FF',
                    'color' => '#7C3AED',
                    'label' => 'Pengelolaan Akun'
                ];
            case 'retensi_arsip':
            case 'retensi_inaktif_arsip':
                return [
                    'icon'  => 'bi-clock-history',
                    'bg'    => '#FEE2E2',
                    'color' => '#DC2626',
                    'label' => 'Retensi JRA'
                ];
            default:
                return [
                    'icon'  => 'bi-bell-fill',
                    'bg'    => '#F1F5F9',
                    'color' => '#64748B',
                    'label' => 'Notifikasi'
                ];
        }
    }

    /**
     * Format Waktu Relatif Human Readable (Bahasa Indonesia)
     */
    private function formatTimeAgo(?string $datetime): string
    {
        if (empty($datetime)) {
            return '-';
        }

        $time = strtotime($datetime);
        $diff = time() - $time;

        if ($diff < 60) {
            return 'Baru saja';
        }

        $mins = round($diff / 60);
        if ($mins < 60) {
            return $mins . ' menit lalu';
        }

        $hours = round($diff / 3600);
        if ($hours < 24) {
            return $hours . ' jam lalu';
        }

        $days = round($diff / 86400);
        if ($days === 1) {
            return 'Kemarin, ' . date('H:i', $time);
        }

        if ($days < 7) {
            return $days . ' hari lalu';
        }

        return date('d M Y, H:i', $time);
    }
}
