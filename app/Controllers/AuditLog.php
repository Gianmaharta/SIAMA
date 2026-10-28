<?php

namespace App\Controllers;

use App\Models\AccessLogModel;
use App\Models\ActivityLogModel;

class AuditLog extends BaseController
{
    protected $accessLogModel;
    protected $activityLogModel;

    protected const PER_PAGE = 25;

    public function __construct()
    {
        $this->accessLogModel   = new AccessLogModel();
        $this->activityLogModel = new ActivityLogModel();
    }

    /**
     * Tampilkan halaman Access Log (Rekam Jejak Akses Sistem).
     * Hanya Admin_Pemkab yang dapat mengakses halaman ini.
     */
    public function accessLog()
    {
        // Ambil parameter filter dari query string
        $search    = $this->request->getGet('search');
        $action    = $this->request->getGet('action');
        $periode   = $this->request->getGet('periode');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');
        $perPage   = (int) ($this->request->getGet('per_page') ?? 10);
        if ($perPage <= 0) $perPage = 10;

        if ($periode === 'today') {
            $startDate = date('Y-m-d');
            $endDate   = date('Y-m-d');
        } elseif ($periode === 'week') {
            $startDate = date('Y-m-d', strtotime('-7 days'));
            $endDate   = date('Y-m-d');
        } elseif ($periode === 'month' || $periode === 'month_30') {
            $startDate = date('Y-m-d', strtotime('-30 days'));
            $endDate   = date('Y-m-d');
        }

        $builder = $this->accessLogModel->getAccessLogsWithFilter($startDate, $endDate, $search, $action);

        // Hitung total untuk paginasi
        $total = (clone $builder)->countAllResults();

        // Atur paginasi
        $pager   = \Config\Services::pager();
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * $perPage;

        $logs = $builder->limit($perPage, $offset)->get()->getResultArray();

        $data = [
            'logs'       => $logs,
            'pager'      => $pager->makeLinks($page, $perPage, $total, 'default_full'),
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'filters'    => [
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'search'     => $search,
                'action'     => $action,
                'periode'    => $periode,
            ],
        ];

        return view('audit_log/access', $data);
    }

    /**
     * Export data Access Log ke format CSV sesuai filter yang aktif.
     */
    public function exportAccessCsv()
    {
        $search    = $this->request->getGet('search');
        $action    = $this->request->getGet('action');
        $periode   = $this->request->getGet('periode');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        if ($periode === 'today') {
            $startDate = date('Y-m-d');
            $endDate   = date('Y-m-d');
        } elseif ($periode === 'week') {
            $startDate = date('Y-m-d', strtotime('-7 days'));
            $endDate   = date('Y-m-d');
        } elseif ($periode === 'month' || $periode === 'month_30') {
            $startDate = date('Y-m-d', strtotime('-30 days'));
            $endDate   = date('Y-m-d');
        }

        $builder = $this->accessLogModel->getAccessLogsWithFilter($startDate, $endDate, $search, $action);
        $logs = $builder->get()->getResultArray();

        $filename = 'Access_Log_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, ['No', 'Waktu', 'Username', 'IP Address', 'Aksi', 'Keterangan']);

        $no = 1;
        foreach ($logs as $row) {
            $username = 'system';
            if (!empty($row['nama_user'])) {
                $cleanName = preg_replace('/\s*\(.*?\)\s*/', '', $row['nama_user']);
                $username = strtolower(str_replace(' ', '.', trim($cleanName)));
            } elseif (!empty($row['email'])) {
                $parts = explode('@', $row['email']);
                $username = $parts[0];
            }
            if ($username === 'administrator.pemkab' || $username === 'admin.pemkab') {
                $username = 'admin_pemkab';
            }

            $rawEvent = strtolower($row['event'] ?? '');
            $browser = 'Chrome';
            $ua = $row['user_agent'] ?? '';
            if (!empty($ua)) {
                if (stripos($ua, 'Edg') !== false) {
                    $browser = 'Edge';
                } elseif (stripos($ua, 'Firefox') !== false) {
                    $browser = 'Firefox';
                } elseif (stripos($ua, 'Safari') !== false && stripos($ua, 'Chrome') === false) {
                    $browser = 'Safari';
                } elseif (stripos($ua, 'Chrome') !== false) {
                    $browser = 'Chrome';
                }
            }

            if (str_contains($rawEvent, 'login') && !str_contains($rawEvent, 'fail')) {
                $aksi = 'Login';
                $ket = 'Login berhasil dari browser ' . $browser;
            } elseif (str_contains($rawEvent, 'logout')) {
                $aksi = 'Logout';
                $ket = 'Sesi berakhir oleh pengguna';
            } elseif (str_contains($rawEvent, 'fail')) {
                $aksi = 'Gagal';
                $ket = 'Percobaan login gagal (kredensial tidak valid)';
            } else {
                $aksi = ucfirst($row['event']);
                $ket = 'Aktivitas akses sistem';
            }

            $ip = $row['ip_address'] === '::1' ? '127.0.0.1' : $row['ip_address'];

            fputcsv($output, [
                $no++,
                $row['created_at'],
                $username,
                $ip,
                $aksi,
                $ket
            ]);
        }
        fclose($output);
        exit();
    }

    /**
     * Tampilkan halaman Activity Log (Rekam Jejak Aktivitas Operasional).
     * Hanya Admin_Pemkab yang dapat mengakses halaman ini.
     */
    public function activityLog()
    {
        // Ambil parameter filter dari query string
        $search    = $this->request->getGet('search');
        $module    = $this->request->getGet('module');
        $status    = $this->request->getGet('status');
        $periode   = $this->request->getGet('periode');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');
        $perPage   = (int) ($this->request->getGet('per_page') ?? 10);
        if ($perPage <= 0) $perPage = 10;

        // Penanganan periode filter
        if ($periode === 'today') {
            $startDate = date('Y-m-d');
            $endDate   = date('Y-m-d');
        } elseif ($periode === 'week') {
            $startDate = date('Y-m-d', strtotime('-7 days'));
            $endDate   = date('Y-m-d');
        } elseif ($periode === 'month') {
            $startDate = date('Y-m-01');
            $endDate   = date('Y-m-t');
        } elseif ($periode === 'year') {
            $startDate = date('Y-01-01');
            $endDate   = date('Y-12-31');
        }

        $builder = $this->activityLogModel->getActivityLogsWithFilter($startDate, $endDate, $module, $search);

        if ($status === 'Gagal') {
            $builder->where('1 = 0');
        }

        // Hitung total untuk paginasi
        $total = (clone $builder)->countAllResults();

        // Atur paginasi
        $pager   = \Config\Services::pager();
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * $perPage;

        $logs = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Ambil daftar modul unik untuk dropdown filter
        $modules = $this->activityLogModel->getDistinctModules();

        $data = [
            'logs'       => $logs,
            'modules'    => $modules,
            'pager'      => $pager->makeLinks($page, $perPage, $total, 'default_full'),
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'filters'    => [
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'module'     => $module,
                'search'     => $search,
                'status'     => $status,
                'periode'    => $periode,
            ],
        ];

        return view('audit_log/activity', $data);
    }

    /**
     * Export data Activity Log ke format CSV sesuai filter yang aktif.
     */
    public function exportActivityCsv()
    {
        $search    = $this->request->getGet('search');
        $module    = $this->request->getGet('module');
        $status    = $this->request->getGet('status');
        $periode   = $this->request->getGet('periode');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        if ($periode === 'today') {
            $startDate = date('Y-m-d');
            $endDate   = date('Y-m-d');
        } elseif ($periode === 'week') {
            $startDate = date('Y-m-d', strtotime('-7 days'));
            $endDate   = date('Y-m-d');
        } elseif ($periode === 'month') {
            $startDate = date('Y-m-01');
            $endDate   = date('Y-m-t');
        } elseif ($periode === 'year') {
            $startDate = date('Y-01-01');
            $endDate   = date('Y-12-31');
        }

        $builder = $this->activityLogModel->getActivityLogsWithFilter($startDate, $endDate, $module, $search);

        if ($status === 'Gagal') {
            $builder->where('1 = 0');
        }

        $logs = $builder->get()->getResultArray();

        $filename = 'Activity_Log_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, ['No', 'Waktu', 'Username', 'Modul', 'Aksi', 'Detail', 'Status']);

        $no = 1;
        foreach ($logs as $row) {
            $username = !empty($row['email']) ? explode('@', $row['email'])[0] : ($row['nama_user'] ?? 'system');
            fputcsv($output, [
                $no++,
                $row['created_at'],
                $username,
                $row['module'],
                $row['action'],
                $row['description'],
                'Berhasil'
            ]);
        }
        fclose($output);
        exit();
    }
}
