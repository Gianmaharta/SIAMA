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
        'status_persetujuan',
        'file_ba_pdf',
        'id_pelaksana',
        'id_kabid',
        'id_pimpinan',
        'catatan_revisi',
        'created_by',
        'updated_by'
    ];

    // Gunakan useTimestamps agar created_at dan updated_at terisi otomatis
    protected $useTimestamps = true;

    public function countAllGlobal()
    {
        return $this->countAllResults();
    }

    public function countMenungguVerifikasiKabid($id_kabid)
    {
        return $this->where('id_kabid', $id_kabid)
                    ->where('status_persetujuan', 'Draf_Kabid')
                    ->countAllResults();
    }

    public function countMenungguTtdPimpinan($id_pimpinan)
    {
        return $this->where('id_pimpinan', $id_pimpinan)
                    ->where('status_persetujuan', 'Draf_Pimpinan')
                    ->countAllResults();
    }

    public function getBeritaAcaraWithRelations($id = null, $id_opd = null)
    {
        $builder = $this->db->table('berita_acara');
        $builder->select('berita_acara.*, 
                          opd.nama_opd, 
                          spt.nomor_spt, 
                          pelaksana.nama as nama_pelaksana,
                          kabid.nama as nama_kabid,
                          pimpinan.nama as nama_pimpinan');
        $builder->join('opd', 'opd.id_opd = berita_acara.id_opd', 'left');
        $builder->join('spt', 'spt.id_spt = berita_acara.id_spt', 'left');
        $builder->join('users pelaksana', 'pelaksana.id_user = berita_acara.id_pelaksana', 'left');
        $builder->join('users kabid', 'kabid.id_user = berita_acara.id_kabid', 'left');
        $builder->join('users pimpinan', 'pimpinan.id_user = berita_acara.id_pimpinan', 'left');

        if ($id) {
            $builder->where('berita_acara.id_berita_acara', $id);
            return $builder->get()->getRowArray();
        }

        if ($id_opd) {
            $builder->where('berita_acara.id_opd', $id_opd);
        }

        $builder->orderBy('berita_acara.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }
}
