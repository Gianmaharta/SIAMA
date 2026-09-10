<?php

namespace App\Models;

use CodeIgniter\Model;

class ArsipModel extends Model
{
    protected $table            = 'arsip';
    protected $primaryKey       = 'id_arsip';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    public function countAllGlobal()
    {
        return $this->countAllResults();
    }

    public function countByOpd($id_opd)
    {
        return $this->where('id_opd', $id_opd)->countAllResults();
    }

    public function countByBidang($id_bidang)
    {
        return $this->where('id_bidang', $id_bidang)->countAllResults();
    }
}
