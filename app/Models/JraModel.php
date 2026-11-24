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
    protected $beforeUpdate  = [];
    
    public function getJraWithKlasifikasi($id = null, ?int $limit = null, int $offset = 0)
    {
        $builder = $this->select('jra.*, kode_klasifikasi.kode, kode_klasifikasi.nama_klasifikasi, kode_klasifikasi.deskripsi')
                        ->join('kode_klasifikasi', 'kode_klasifikasi.id_kode_klasifikasi = jra.id_kode_klasifikasi')
                        ->orderBy('kode_klasifikasi.kode', 'ASC')
                        ->orderBy('jra.created_at', 'ASC');
        if ($id) {
            return $builder->where('jra.id_jra', $id)->first();
        }
        if ($limit !== null) {
            return $builder->findAll($limit, $offset);
        }
        return $builder->findAll();
    }

    /**
     * Server-Side DataTables query untuk Master JRA (2.444+ baris)
     */
    public function getJraDatatable(
        int $start = 0,
        int $length = 25,
        string $search = '',
        string $orderCol = 'kode_klasifikasi.kode',
        string $orderDir = 'ASC'
    ): array {
        $allowedOrderCols = [
            'jra.created_at',
            'kode_klasifikasi.kode',
            'jra.retensi_aktif',
            'jra.retensi_inaktif',
            'jra.keterangan_retensi',
            'jra.klasifikasi_keamanan',
            'jra.dasar_pertimbangan',
        ];
        if (!in_array($orderCol, $allowedOrderCols, true)) {
            $orderCol = 'kode_klasifikasi.kode';
        }
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

        $recordsTotal = (int) $this->db->table($this->table)->countAllResults();

        $baseBuilder = $this->db->table($this->table)
            ->select('jra.*, kode_klasifikasi.kode, kode_klasifikasi.nama_klasifikasi, kode_klasifikasi.deskripsi')
            ->join('kode_klasifikasi', 'kode_klasifikasi.id_kode_klasifikasi = jra.id_kode_klasifikasi');

        if ($search !== '') {
            $baseBuilder->groupStart()
                ->like('kode_klasifikasi.kode', $search)
                ->orLike('kode_klasifikasi.nama_klasifikasi', $search)
                ->orLike('kode_klasifikasi.deskripsi', $search)
                ->orLike('jra.keterangan_retensi', $search)
                ->orLike('jra.klasifikasi_keamanan', $search)
                ->orLike('jra.dasar_pertimbangan', $search)
                ->groupEnd();
        }

        $countBuilder = clone $baseBuilder;
        $recordsFiltered = (int) $countBuilder->countAllResults();

        $data = $baseBuilder
            ->orderBy($orderCol, $orderDir)
            ->orderBy('kode_klasifikasi.kode', 'ASC')
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        return [
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ];
    }
    
    public function getByKlasifikasi($id_kode_klasifikasi)
    {
        return $this->where('id_kode_klasifikasi', $id_kode_klasifikasi)->first();
    }
}
