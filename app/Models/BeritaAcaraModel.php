<?php

namespace App\Models;

use App\Models\BaseModel;

class BeritaAcaraModel extends BaseModel
{
    protected $table            = 'berita_acara';
    protected $primaryKey       = 'id_berita_acara';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_berita_acara',
        'id_opd',
        'id_spt',
        'nomor_ba',
        'tanggal_ba',
        'status',
        'file_ba',
        'id_pembuat',
        'id_verifikator',
        'id_pimpinan',
        'created_by',
        'updated_by'
    ];

    public function countAllGlobal()
    {
        return $this->countAllResults();
    }

    public function countMenungguVerifikasiKabid($id_kabid)
    {
        return $this->where('id_kabid', $id_kabid)
                    ->where('status_persetujuan', 'Menunggu Verifikasi Kabid') // Asumsi status
                    ->countAllResults();
    }

    public function countMenungguTtdPimpinan($id_pimpinan)
    {
        return $this->where('id_pimpinan', $id_pimpinan)
                    ->where('status_persetujuan', 'Menunggu TTD Pimpinan') // Asumsi status
                    ->countAllResults();
    }
}
