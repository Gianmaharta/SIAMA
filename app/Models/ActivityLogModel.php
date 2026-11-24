<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table      = 'activity_log';
    protected $primaryKey = 'id';

    // Tidak menggunakan BaseModel karena log bersifat read-only dari UI
    // dan tabel ini tidak memiliki kolom updated_at / created_by
    public function getActivityLogsWithFilter($startDate = null, $endDate = null, $module = null, $search = null)
    {
        $builder = $this->db->table('activity_log al');
        $builder->select([
            'al.id AS id_activity_log',
            'al.module',
            'al.action',
            'al.description',
            'al.old_data',
            'al.new_data',
            'al.created_at',
            'u.nama AS nama_user',
            'u.email',
            'o.nama_opd',
        ]);
        $builder->join('users u', 'u.id_user = al.user_id', 'left');
        $builder->join('opd o', 'o.id_opd = u.id_opd', 'left');

        if ($startDate) {
            $builder->where('al.created_at >=', $startDate . ' 00:00:00');
        }
        if ($endDate) {
            $builder->where('al.created_at <=', $endDate . ' 23:59:59');
        }
        if ($module) {
            $builder->where('al.module', $module);
        }
        if ($search) {
            $builder->groupStart()
                ->like('u.nama', $search)
                ->orLike('al.description', $search)
                ->orLike('al.action', $search)
                ->orLike('al.module', $search)
                ->orLike('o.nama_opd', $search)
            ->groupEnd();
        }

        $builder->orderBy('al.created_at', 'DESC');

        return $builder;
    }

    /**
     * Ambil daftar modul unik yang pernah tercatat, untuk isian dropdown filter.
     */
    public function getDistinctModules(): array
    {
        return $this->db->table('activity_log')
            ->select('module')
            ->distinct()
            ->orderBy('module', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Menghasilkan data statistik visualisasi grafik untuk halaman Activity Log (Catatan #11)
     */
    public function getActivityChartStats(?string $startDate = null, ?string $endDate = null, ?string $module = null, ?string $search = null): array
    {
        // 1. Tren Aktivitas Harian (7 Hari Terakhir atau sesuai filter rentang tanggal)
        $trendLabels = [];
        $trendValues = [];

        if (!empty($startDate) && !empty($endDate) && $startDate !== $endDate) {
            $startTs = strtotime($startDate);
            $endTs   = strtotime($endDate);
            $diffDays = max(1, min(30, (int) round(($endTs - $startTs) / 86400)));
            for ($i = $diffDays; $i >= 0; $i--) {
                $d = date('Y-m-d', $endTs - ($i * 86400));
                $trendLabels[] = date('d M', strtotime($d));
                $b = $this->getActivityLogsWithFilter($d, $d, $module, $search);
                $trendValues[] = (int) $b->countAllResults();
            }
        } else {
            for ($i = 6; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-{$i} days"));
                $trendLabels[] = date('d M', strtotime($d));
                $b = $this->getActivityLogsWithFilter($d, $d, $module, $search);
                $trendValues[] = (int) $b->countAllResults();
            }
        }

        // 2. Distribusi Aktivitas per Modul
        $modBuilder = $this->db->table('activity_log al')
            ->select('al.module, COUNT(*) as total')
            ->join('users u', 'u.id_user = al.user_id', 'left')
            ->join('opd o', 'o.id_opd = u.id_opd', 'left');

        if ($startDate) {
            $modBuilder->where('al.created_at >=', $startDate . ' 00:00:00');
        }
        if ($endDate) {
            $modBuilder->where('al.created_at <=', $endDate . ' 23:59:59');
        }
        if ($module) {
            $modBuilder->where('al.module', $module);
        }
        if ($search) {
            $modBuilder->groupStart()
                ->like('u.nama', $search)
                ->orLike('al.description', $search)
                ->orLike('al.action', $search)
                ->orLike('al.module', $search)
                ->orLike('o.nama_opd', $search)
            ->groupEnd();
        }

        $modRows = $modBuilder
            ->groupBy('al.module')
            ->orderBy('total', 'DESC')
            ->limit(8)
            ->get()
            ->getResultArray();

        $moduleLabels = [];
        $moduleValues = [];
        foreach ($modRows as $mRow) {
            $moduleLabels[] = !empty($mRow['module']) ? $mRow['module'] : 'Sistem';
            $moduleValues[] = (int) $mRow['total'];
        }

        // 3. Distribusi Berdasarkan Jenis Aksi (CREATE, UPDATE, DELETE, dll.)
        $actBuilder = $this->db->table('activity_log al')
            ->select('UPPER(al.action) as action_name, COUNT(*) as total')
            ->join('users u', 'u.id_user = al.user_id', 'left')
            ->join('opd o', 'o.id_opd = u.id_opd', 'left');

        if ($startDate) {
            $actBuilder->where('al.created_at >=', $startDate . ' 00:00:00');
        }
        if ($endDate) {
            $actBuilder->where('al.created_at <=', $endDate . ' 23:59:59');
        }
        if ($module) {
            $actBuilder->where('al.module', $module);
        }
        if ($search) {
            $actBuilder->groupStart()
                ->like('u.nama', $search)
                ->orLike('al.description', $search)
                ->orLike('al.action', $search)
                ->orLike('al.module', $search)
                ->orLike('o.nama_opd', $search)
            ->groupEnd();
        }

        $actRows = $actBuilder
            ->groupBy('UPPER(al.action)')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();

        $actionLabels = [];
        $actionValues = [];
        foreach ($actRows as $aRow) {
            $actionLabels[] = !empty($aRow['action_name']) ? $aRow['action_name'] : 'LAINNYA';
            $actionValues[] = (int) $aRow['total'];
        }

        return [
            'daily_trend' => [
                'labels' => $trendLabels,
                'values' => $trendValues,
            ],
            'by_module' => [
                'labels' => $moduleLabels,
                'values' => $moduleValues,
            ],
            'by_action' => [
                'labels' => $actionLabels,
                'values' => $actionValues,
            ],
        ];
    }
}
