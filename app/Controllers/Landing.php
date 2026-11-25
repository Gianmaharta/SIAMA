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

        $keyword = trim((string) ($this->request->getVar('q') ?? $this->request->getVar('keyword') ?? ''));
        $searchResults = [];
        $searchPerformed = false;

        if ($keyword !== '' && mb_strlen($keyword) >= 2) {
            $searchPerformed = true;
            $searchResults = $this->queryPublicArsip($db, $keyword);
        }

        $payload = [
            'settings'        => get_all_settings(),
            'stats'           => $stats,
            'isLoggedIn'      => (bool) session()->get('isLoggedIn'),
            'searchKeyword'   => $keyword,
            'searchPerformed' => $searchPerformed,
            'searchResults'   => $searchResults,
        ];

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $payload,
            ]);
        }

        return view('landing/index', $payload);
    }

    /**
     * Endpoint API / AJAX untuk Pencarian Informasi Arsip Publik
     */
    public function search()
    {
        $keyword = trim((string) ($this->request->getVar('q') ?? $this->request->getVar('keyword') ?? ''));

        if ($keyword === '') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Silakan masukkan nomor arsip, nomor berita acara, atau kata kunci pencarian.',
                'data'    => [],
                'count'   => 0,
            ]);
        }

        if (mb_strlen($keyword) < 2) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Kata kunci pencarian minimal 2 karakter.',
                'data'    => [],
                'count'   => 0,
            ]);
        }

        $db = \Config\Database::connect();
        $results = $this->queryPublicArsip($db, $keyword);

        return $this->response->setJSON([
            'status'  => 'success',
            'keyword' => $keyword,
            'count'   => count($results),
            'data'    => $results,
        ]);
    }

    /**
     * Query data arsip publik dengan relasi aman (tidak mengekspos lokasi file fisik secara publik)
     */
    private function queryPublicArsip($db, string $keyword): array
    {
        try {
            if (!$db->tableExists('arsip')) {
                return [];
            }

            $builder = $db->table('arsip')
                ->select([
                    'arsip.id_arsip',
                    'arsip.nomor_arsip',
                    'arsip.nama_arsip',
                    'arsip.kurun_waktu',
                    'arsip.tingkat_perkembangan',
                    'arsip.kondisi',
                    'arsip.status_verifikasi',
                    'arsip.status_autentikasi',
                    'arsip.created_at',
                    'opd.nama_opd',
                    'bidang.nama_bidang',
                    'kode_klasifikasi.kode AS kode_klasifikasi',
                    'kode_klasifikasi.nama_klasifikasi',
                    'berita_acara.nomor_ba',
                    'berita_acara.tanggal_ba',
                    'berita_acara.status_persetujuan AS status_ba',
                    'jra.klasifikasi_keamanan',
                    'jra.hak_akses',
                    'jra.akses_publik',
                ])
                ->join('opd', 'opd.id_opd = arsip.id_opd', 'left')
                ->join('bidang', 'bidang.id_bidang = arsip.id_bidang', 'left')
                ->join('kode_klasifikasi', 'kode_klasifikasi.id_kode_klasifikasi = arsip.id_kode_klasifikasi', 'left')
                ->join('berita_acara', 'berita_acara.id_berita_acara = arsip.id_berita_acara', 'left')
                ->join('jra', 'jra.id_kode_klasifikasi = arsip.id_kode_klasifikasi', 'left')
                ->groupStart()
                    ->like('arsip.nomor_arsip', $keyword)
                    ->orLike('arsip.nama_arsip', $keyword)
                    ->orLike('berita_acara.nomor_ba', $keyword)
                    ->orLike('kode_klasifikasi.kode', $keyword)
                    ->orLike('kode_klasifikasi.nama_klasifikasi', $keyword)
                    ->orLike('opd.nama_opd', $keyword)
                ->groupEnd()
                ->orderBy('arsip.created_at', 'DESC')
                ->limit(10);

            $rows = $builder->get()->getResultArray();
            $formatted = [];

            foreach ($rows as $row) {
                // Tentukan status keterbukaan informasi arsip
                $aksesPublikRaw = strtolower((string) ($row['akses_publik'] ?? ''));
                $hakAksesRaw    = strtolower((string) ($row['hak_akses'] ?? ''));
                $keamananRaw    = strtolower((string) ($row['klasifikasi_keamanan'] ?? ''));

                $isTerbuka = false;
                if (str_contains($aksesPublikRaw, 'terbuka') || str_contains($hakAksesRaw, 'terbuka') || $keamananRaw === 'biasa') {
                    $isTerbuka = true;
                }

                $formatted[] = [
                    'id_arsip'           => $row['id_arsip'],
                    'nomor_arsip'        => $row['nomor_arsip'] ?: '-',
                    'nama_arsip'         => $row['nama_arsip'] ?: 'Tanpa Judul',
                    'nama_opd'           => $row['nama_opd'] ?: 'Pemerintah Kabupaten Buleleng',
                    'nama_bidang'        => $row['nama_bidang'] ?: '-',
                    'kode_klasifikasi'   => $row['kode_klasifikasi'] ? ($row['kode_klasifikasi'] . ' - ' . ($row['nama_klasifikasi'] ?? '')) : '-',
                    'nomor_ba'           => $row['nomor_ba'] ?: '-',
                    'tanggal_ba'         => $row['tanggal_ba'] ? date('d/m/Y', strtotime($row['tanggal_ba'])) : '-',
                    'kurun_waktu'        => $row['kurun_waktu'] ?: '-',
                    'kondisi'            => $row['kondisi'] ?: 'Baik',
                    'status_autentikasi' => $row['status_autentikasi'] ?: 'Terdaftar di Sistem',
                    'status_ba'          => $row['status_ba'] ?: 'Diproses',
                    'akses_publik'       => $isTerbuka ? 'Informasi Terbuka' : 'Internal Kedinasan',
                    'is_terbuka'         => $isTerbuka,
                ];
            }

            return $formatted;
        } catch (\Throwable $e) {
            log_message('error', 'Gagal query public arsip di Landing: ' . $e->getMessage());
            return [];
        }
    }
}
