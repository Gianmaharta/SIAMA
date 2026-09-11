<?php

namespace App\Models;

use CodeIgniter\Model;

class BidangModel extends Model
{
    protected $table            = 'bidang';
    protected $primaryKey       = 'id_bidang';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_opd', 'nama_bidang'];

    // Validasi
    protected $validationRules      = [
        'id_opd'      => 'required|numeric',
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
     * @param int|null $id_opd Filter berdasarkan OPD tertentu jika diisi
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
}
