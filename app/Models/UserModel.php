<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'email', 
        'password', 
        'nama', 
        'nip',
        'id_opd', 
        'id_bidang',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = false;
}
