<?php

namespace App\Models;

use CodeIgniter\Model;

class BeritaAcaraModel extends Model
{
    protected $table            = 'berita_acara';
    protected $primaryKey       = 'id_berita_acara';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

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
