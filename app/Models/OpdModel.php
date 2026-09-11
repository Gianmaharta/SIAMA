<?php

namespace App\Models;

use CodeIgniter\Model;

class OpdModel extends Model
{
    protected $table            = 'opd';
    protected $primaryKey       = 'id_opd';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kode_opd', 'nama_opd', 'kuota_storage_mb', 'is_active'];

    // Validasi
    protected $validationRules      = [
        'kode_opd' => 'required|max_length[50]',
        'nama_opd' => 'required|max_length[255]'
    ];
    protected $validationMessages   = [
        'kode_opd' => [
            'required' => 'Kode OPD harus diisi.'
        ],
        'nama_opd' => [
            'required' => 'Nama OPD harus diisi.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
