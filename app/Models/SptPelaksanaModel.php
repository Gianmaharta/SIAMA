<?php

namespace App\Models;

use App\Models\BaseModel;

class SptPelaksanaModel extends BaseModel
{
    protected $table            = 'spt_pelaksana';
    // Gunakan array untuk composite key, meski CI4 basic model kurang mensupport penuh,
    // kita tetap set protectFields untuk insert data
    protected $primaryKey       = 'id_spt';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    // id_bidang juga ada di tabel berdasarkan migration 2026-01-01-000008_CreateSptPelaksanaTable.php
    protected $allowedFields    = ['id_spt', 'id_user', 'id_bidang']; 

    /**
     * Mengambil daftar pelaksana berdasarkan ID SPT
     *
     * @param int $id_spt
     * @return array
     */
    public function getPelaksanaBySpt($id_spt)
    {
        return $this->select('users.nama, users.email')
                    ->join('users', 'users.id_user = spt_pelaksana.id_user')
                    ->where('spt_pelaksana.id_spt', $id_spt)
                    ->findAll();
    }
}
