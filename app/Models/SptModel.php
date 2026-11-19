<?php

namespace App\Models;

use App\Models\BaseModel;

class SptModel extends BaseModel
{
    protected $table            = 'spt';
    protected $primaryKey       = 'id_spt';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $beforeInsert     = ['generateUuid'];
    protected $beforeUpdate     = [];

    protected $allowedFields    = [
        'id_spt',
        'nomor_spt', 
        'tanggal_spt', 
        'id_opd', 
        'perihal', 
        'tanggal_mulai', 
        'tanggal_selesai', 
        'status', 
        'status_penugasan',
        'id_pimpinan',
        'file_spt',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    // Validasi
    protected $validationRules      = [
        'nomor_spt'       => 'required|max_length[100]',
        'tanggal_spt'     => 'required|valid_date',
        'perihal'         => 'required',
        'tanggal_mulai'   => 'required|valid_date',
        'tanggal_selesai' => 'required|valid_date'
    ];
    protected $validationMessages   = [
        'nomor_spt' => [
            'required' => 'Nomor SPT harus diisi.'
        ],
        'tanggal_spt' => [
            'required' => 'Tanggal SPT harus diisi.',
            'valid_date' => 'Format Tanggal SPT tidak valid.'
        ],
        'perihal' => [
            'required' => 'Perihal harus diisi.'
        ],
        'tanggal_mulai' => [
            'required' => 'Tanggal Mulai harus diisi.',
            'valid_date' => 'Format Tanggal Mulai tidak valid.'
        ],
        'tanggal_selesai' => [
            'required' => 'Tanggal Selesai harus diisi.',
            'valid_date' => 'Format Tanggal Selesai tidak valid.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Untuk perhitungan dashboard global
    public function countAllGlobal()
    {
        return $this->countAllResults();
    }

    public function countByOpd($id_opd)
    {
        return $this->where('id_opd', $id_opd)->countAllResults();
    }

    // Mendapatkan daftar SPT berdasarkan OPD (Terbaru di urutan paling atas)
    public function getSptByOpd($id_opd)
    {
        return $this->select('spt.*, (SELECT COUNT(a.id_arsip) FROM arsip a WHERE a.id_spt = spt.id_spt) as jumlah_arsip')
                    ->where('spt.id_opd', $id_opd)
                    ->orderBy('COALESCE(spt.created_at, CONCAT(spt.tanggal_spt, " 00:00:00"))', 'DESC', false)
                    ->orderBy('spt.tanggal_spt', 'DESC')
                    ->orderBy('spt.id_spt', 'DESC')
                    ->findAll();
    }

    // Mendapatkan daftar SPT penugasan Arsiparis (Tugas terbaru di urutan paling atas)
    public function getSptByPelaksana($user_id)
    {
        return $this->select('spt.*, spt_assignments.id_assignment, spt_assignments.status as assignment_status, spt_assignments.created_at as tanggal_penugasan, spt_assignments.assigned_by, (SELECT COUNT(a.id_arsip) FROM arsip a WHERE a.id_spt = spt.id_spt) as jumlah_arsip, u_kabid.nama as nama_kabid')
                    ->join('spt_assignments', 'spt_assignments.id_spt = spt.id_spt')
                    ->join('users u_kabid', 'u_kabid.id_user = spt_assignments.assigned_by', 'left')
                    ->where('spt_assignments.id_user', $user_id)
                    ->orderBy('COALESCE(spt_assignments.created_at, spt.created_at, CONCAT(spt.tanggal_spt, " 00:00:00"))', 'DESC', false)
                    ->orderBy('spt.tanggal_spt', 'DESC')
                    ->orderBy('spt_assignments.id_assignment', 'DESC')
                    ->findAll();
    }

    // Mendapatkan semua data SPT global dengan jumlah arsip (Urutan terbaru)
    public function getAllSptWithArsipCount()
    {
        return $this->select('spt.*, (SELECT COUNT(a.id_arsip) FROM arsip a WHERE a.id_spt = spt.id_spt) as jumlah_arsip')
                    ->orderBy('COALESCE(spt.created_at, CONCAT(spt.tanggal_spt, " 00:00:00"))', 'DESC', false)
                    ->orderBy('spt.tanggal_spt', 'DESC')
                    ->orderBy('spt.id_spt', 'DESC')
                    ->findAll();
    }

    // Untuk perhitungan dashboard pelaksana
    public function countByPelaksana($id_user)
    {
        return $this->join('spt_assignments', 'spt.id_spt = spt_assignments.id_spt')
                    ->where('spt_assignments.id_user', $id_user)
                    ->countAllResults();
    }
}
