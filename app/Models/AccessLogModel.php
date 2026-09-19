<?php

namespace App\Models;

use CodeIgniter\Model;

class AccessLogModel extends Model
{
    protected $table      = 'access_log';
    protected $primaryKey = 'id';

    // Tidak menggunakan BaseModel karena log bersifat read-only dari UI
    // dan tabel ini tidak memiliki kolom updated_at / created_by
    public function getAccessLogsWithFilter($startDate = null, $endDate = null, $search = null)
    {
        $builder = $this->db->table('access_log al');
        $builder->select([
            'al.id AS id_access_log',
            'al.event',
            'al.ip_address',
            'al.user_agent',
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
        if ($search) {
            $builder->groupStart()
                ->like('u.nama', $search)
                ->orLike('al.ip_address', $search)
                ->orLike('al.event', $search)
                ->orLike('o.nama_opd', $search)
            ->groupEnd();
        }

        $builder->orderBy('al.created_at', 'DESC');

        return $builder;
    }
}
