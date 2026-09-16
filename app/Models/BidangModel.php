<?php

namespace App\Models;

use App\Models\BaseModel;

class BidangModel extends BaseModel
{
    protected $table            = 'bidang';
    protected $primaryKey       = 'id_bidang';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_bidang', 'id_opd', 'nama_bidang', 'created_by', 'updated_by'];

    // Validasi
    protected $validationRules      = [
        'id_opd'      => 'required',
        'nama_bidang' => 'required|max_length[255]'
    ];
    protected $validationMessages   = [
        'id_opd' => [
            'required' => 'OPD harus dipilih.'
        ],
        'nama_bidang' => [
            'required' => 'Nama Bidang harus diisi.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Mengambil data bidang beserta nama OPD-nya
     *
     * @param string|null $id_opd Filter berdasarkan OPD tertentu jika diisi
     * @return array
     */
    public function getBidangWithOpd($id_opd = null)
    {
        $builder = $this->builder();
        $builder->select('bidang.*, opd.nama_opd');
        $builder->join('opd', 'opd.id_opd = bidang.id_opd');

        if ($id_opd !== null) {
            $builder->where('bidang.id_opd', $id_opd);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Sinkronisasi data dari API Eksternal
     */
    public function syncFromApi($apiData)
    {
        foreach ($apiData as $data) {
            $existing = $this->where('nama_bidang', $data['nama_bidang'])
                             ->where('id_opd', $data['id_opd'])
                             ->first();
            if ($existing) {
                $this->update($existing['id_bidang'], $data);
            } else {
                $this->insert($data);
            }
        }
    }
}
