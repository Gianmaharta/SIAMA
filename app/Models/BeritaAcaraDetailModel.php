<?php

namespace App\Models;

use App\Models\BaseModel;

class BeritaAcaraDetailModel extends BaseModel
{
    protected $table            = 'berita_acara_detail';
    protected $primaryKey       = 'id_ba_detail'; // Tabel ini aslinya punya PK UUID tidak?
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ba_detail',
        'id_berita_acara',
        'id_arsip'
    ];

    public function getDetailArsipByBaId($id_berita_acara)
    {
        $builder = $this->db->table('berita_acara_detail');
        $builder->select('berita_acara_detail.*, arsip.nomor_arsip, arsip.nama_arsip, arsip.kurun_waktu, arsip.kondisi');
        $builder->join('arsip', 'arsip.id_arsip = berita_acara_detail.id_arsip');
        $builder->where('berita_acara_detail.id_berita_acara', $id_berita_acara);
        
        return $builder->get()->getResultArray();
    }
}
