<?php

namespace App\Models;

class JraModel extends BaseModel
{
    protected $table            = 'jra';
    protected $primaryKey       = 'id_jra';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields    = [
        'id_jra',
        'id_kode_klasifikasi',
        'retensi_aktif',
        'retensi_inaktif',
        'keterangan_retensi',
        'klasifikasi_keamanan',
        'hak_akses',
        'akses_publik',
        'dasar_pertimbangan',
        'unit_pengolah',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $beforeInsert  = ['generateUuid'];
    
    public function getJraWithKlasifikasi($id = null)
    {
        $builder = $this->select('jra.*, kode_klasifikasi.kode, kode_klasifikasi.nama_klasifikasi')
                        ->join('kode_klasifikasi', 'kode_klasifikasi.id_kode_klasifikasi = jra.id_kode_klasifikasi');
        if ($id) {
            return $builder->where('jra.id_jra', $id)->first();
        }
        return $builder->findAll();
    }
    
    public function getByKlasifikasi($id_kode_klasifikasi)
    {
        return $this->where('id_kode_klasifikasi', $id_kode_klasifikasi)->first();
    }
}
