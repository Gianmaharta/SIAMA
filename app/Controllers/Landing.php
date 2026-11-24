<?php

namespace App\Controllers;

class Landing extends BaseController
{
    public function index()
    {
        helper('setting');
        $db = \Config\Database::connect();

        $stats = [
            'total_opd'   => 0,
            'total_arsip' => 0,
            'total_jra'   => 0,
            'total_ba'    => 0,
        ];

        try {
            if ($db->tableExists('opd')) {
                $stats['total_opd'] = (int) $db->table('opd')->countAllResults();
            }
            if ($db->tableExists('arsip')) {
                $stats['total_arsip'] = (int) $db->table('arsip')->countAllResults();
            }
            if ($db->tableExists('jra')) {
                $stats['total_jra'] = (int) $db->table('jra')->countAllResults();
            }
            if ($db->tableExists('berita_acara')) {
                $stats['total_ba'] = (int) $db->table('berita_acara')->where('status_persetujuan', 'Selesai')->countAllResults();
            }
        } catch (\Throwable $e) {
            // Gunakan default 0 jika koneksi DB belum siap
        }

        $payload = [
            'settings'   => get_all_settings(),
            'stats'      => $stats,
            'isLoggedIn' => (bool) session()->get('isLoggedIn'),
        ];

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $payload,
            ]);
        }

        return view('landing/index', $payload);
    }
}
