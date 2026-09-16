<?php

namespace App\Models;

use App\Models\BaseModel;

class OpdModel extends BaseModel
{
    protected $table            = 'opd';
    protected $primaryKey       = 'id_opd';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_opd', 'kode_opd', 'nama_opd', 'kuota_storage_mb', 'is_active', 'created_by', 'updated_by'];

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

    /**
     * Sinkronisasi data dari API Eksternal
     */
    public function syncFromApi($apiData)
    {
        foreach ($apiData as $data) {
            $existing = $this->where('kode_opd', $data['kode_opd'])->first();
            if ($existing) {
                $this->update($existing['id_opd'], $data);
            } else {
                $this->insert($data);
            }
        }
    }
}
