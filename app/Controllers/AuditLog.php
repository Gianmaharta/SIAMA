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
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');
        $search    = $this->request->getGet('search');

        $builder = $this->accessLogModel->getAccessLogsWithFilter($startDate, $endDate, $search);

        // Hitung total untuk paginasi
        $total = (clone $builder)->countAllResults();

        // Atur paginasi
        $pager   = \Config\Services::pager();
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * self::PER_PAGE;

        $logs = $builder->limit(self::PER_PAGE, $offset)->get()->getResultArray();

        $data = [
            'logs'       => $logs,
            'pager'      => $pager->makeLinks($page, self::PER_PAGE, $total, 'default_full'),
            'total'      => $total,
            'filters'    => [
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'search'     => $search,
            ],
        ];

        return view('audit_log/access', $data);
    }

    /**
     * Tampilkan halaman Activity Log (Rekam Jejak Aktivitas Operasional).
     * Hanya Admin_Pemkab yang dapat mengakses halaman ini.
     */
    public function activityLog()
    {
        // Ambil parameter filter dari query string
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');
        $module    = $this->request->getGet('module');
        $search    = $this->request->getGet('search');

        $builder = $this->activityLogModel->getActivityLogsWithFilter($startDate, $endDate, $module, $search);

        // Hitung total untuk paginasi
        $total = (clone $builder)->countAllResults();

        // Atur paginasi
        $pager   = \Config\Services::pager();
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * self::PER_PAGE;

        $logs = $builder->limit(self::PER_PAGE, $offset)->get()->getResultArray();

        // Ambil daftar modul unik untuk dropdown filter
        $modules = $this->activityLogModel->getDistinctModules();

        $data = [
            'logs'       => $logs,
            'modules'    => $modules,
            'pager'      => $pager->makeLinks($page, self::PER_PAGE, $total, 'default_full'),
            'total'      => $total,
            'filters'    => [
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'module'     => $module,
                'search'     => $search,
            ],
        ];

        return view('audit_log/activity', $data);
    }
}
