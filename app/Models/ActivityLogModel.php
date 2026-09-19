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
}
