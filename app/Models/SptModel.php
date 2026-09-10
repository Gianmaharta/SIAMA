<?php

namespace App\Models;

use CodeIgniter\Model;

class SptModel extends Model
{
    protected $table            = 'spt';
    protected $primaryKey       = 'id_spt';
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

    public function countByPelaksana($id_user)
    {
        return $this->join('spt_pelaksana', 'spt.id_spt = spt_pelaksana.id_spt')
                    ->where('spt_pelaksana.id_user', $id_user)
                    ->countAllResults();
    }
}
