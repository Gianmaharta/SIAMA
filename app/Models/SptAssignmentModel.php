<?php

namespace App\Models;

use App\Models\BaseModel;

class SptAssignmentModel extends BaseModel
{
    protected $table            = 'spt_assignments';
    protected $primaryKey       = 'id_assignment';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $useTimestamps    = true;
    protected $beforeInsert     = ['generateUuid'];
    protected $beforeUpdate     = [];

    protected $allowedFields    = [
        'id_assignment',
        'id_spt', 
        'id_user', 
        'assigned_by', 
        'status', 
    ];

    /**
     * Mendapatkan tugas arsiparis beserta detail SPT nya
     */
    public function getAssignmentsByUser($id_user)
    {
        return $this->select('spt_assignments.*, spt.nomor_spt, spt.perihal, spt.tanggal_mulai, spt.tanggal_selesai, spt.file_spt')
                    ->join('spt', 'spt.id_spt = spt_assignments.id_spt')
                    ->where('spt_assignments.id_user', $id_user)
                    ->findAll();
    }
}
