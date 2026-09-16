<?php

namespace App\Models;

use App\Models\BaseModel;

class UserModel extends BaseModel
{
    protected $table            = 'users';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user',
        'email', 
        'password', 
        'nama', 
        'nip',
        'id_opd', 
        'id_bidang',
        'is_active',
        'is_default_password',
        'created_by',
        'updated_by',
    ];

    // Dates
    protected $useTimestamps = false;
}
