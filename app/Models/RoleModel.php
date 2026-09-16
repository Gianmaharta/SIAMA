<?php

namespace App\Models;

use App\Models\BaseModel;

class RoleModel extends BaseModel
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id_role';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_role', 'nama_role', 'deskripsi', 'created_by', 'updated_by'];

    // Dates
    protected $useTimestamps = false;
}
