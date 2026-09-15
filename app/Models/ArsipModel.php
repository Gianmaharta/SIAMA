<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ArsipModel
 *
 * Model untuk pengelolaan data arsip digital (hasil alih media).
 * Tabel referensi: arsip
 * Kolom FK penting:
 *   - id_spt         => tabel spt (nullable)
 *   - id_opd         => tabel opd
 *   - id_bidang      => tabel bidang
 *   - id_klasifikasi => tabel kode_klasifikasi
 *   - created_by     => tabel users
 */
class ArsipModel extends Model
{
    protected $table            = 'arsip';
    protected $primaryKey       = 'id_arsip';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_spt',
        'id_opd',
        'id_bidang',
        'id_klasifikasi',
        'nomor_arsip',
        'nama_arsip',
        'tahun_penciptaan',
        'kategori_jra',
        'kondisi_fisik',
        'metode_alih_media',
        'skor_prioritas',
        'file_digital',
        'status_autentikasi',
        'status_alih_media',
        'created_by',
    ];

    // -------------------------------------------------------------------------
    // Aturan Validasi Model
    // -------------------------------------------------------------------------
    protected $validationRules = [
        'nomor_arsip'      => 'required|max_length[100]',
        'nama_arsip'       => 'required|max_length[255]',
        'id_klasifikasi'   => 'required|numeric',
        'id_opd'           => 'required|numeric',
        'id_bidang'        => 'required|numeric',
        'tahun_penciptaan' => 'permit_empty|numeric',
        'skor_prioritas'   => 'permit_empty|numeric',
    ];

    protected $validationMessages = [
        'nomor_arsip' => [
            'required'   => 'Nomor arsip wajib diisi.',
            'max_length' => 'Nomor arsip maksimal 100 karakter.',
        ],
        'nama_arsip' => [
            'required'   => 'Nama arsip wajib diisi.',
            'max_length' => 'Nama arsip maksimal 255 karakter.',
        ],
        'id_klasifikasi' => [
            'required' => 'Kode klasifikasi wajib dipilih.',
            'numeric'  => 'Kode klasifikasi tidak valid.',
        ],
        'id_opd' => [
            'required' => 'OPD wajib diisi.',
            'numeric'  => 'OPD tidak valid.',
        ],
        'id_bidang' => [
            'required' => 'Bidang wajib dipilih.',
            'numeric'  => 'Bidang tidak valid.',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // =========================================================================
    // Method Helper
    // =========================================================================

    /**
     * getArsipWithRelations
     *
     * Mengambil data arsip beserta data relasi dari tabel-tabel master:
     *   - opd           => nama_opd
     *   - bidang        => nama_bidang
     *   - kode_klasifikasi => kode, nama_klasifikasi
     *   - spt           => nomor_spt (LEFT JOIN karena id_spt nullable)
     *   - users         => nama (created_by)
     *
     * @param int|null $id        Filter berdasarkan id_arsip tertentu (untuk detail)
     * @param int|null $id_opd   Filter berdasarkan OPD tertentu
     * @param int|null $id_bidang Filter berdasarkan Bidang tertentu
     * @return array
     */
    public function getArsipWithRelations($id = null, $id_opd = null, $id_bidang = null)
    {
        $builder = $this->db->table('arsip');

        $builder->select([
            'arsip.*',
            'opd.nama_opd',
            'bidang.nama_bidang',
            'kode_klasifikasi.kode         AS kode_klasifikasi',
            'kode_klasifikasi.nama_klasifikasi',
            'spt.nomor_spt',
            'users.nama                    AS nama_arsiparis',
        ]);

        $builder->join('opd',              'opd.id_opd = arsip.id_opd',                              'left');
        $builder->join('bidang',           'bidang.id_bidang = arsip.id_bidang',                      'left');
        $builder->join('kode_klasifikasi', 'kode_klasifikasi.id_klasifikasi = arsip.id_klasifikasi',  'left');
        $builder->join('spt',              'spt.id_spt = arsip.id_spt',                               'left');
        $builder->join('users',            'users.id_user = arsip.created_by',                        'left');

        if ($id !== null) {
            $builder->where('arsip.id_arsip', $id);
            return $builder->get()->getRowArray();
        }

        if ($id_opd !== null) {
            $builder->where('arsip.id_opd', $id_opd);
        }

        if ($id_bidang !== null) {
            $builder->where('arsip.id_bidang', $id_bidang);
        }

        $builder->orderBy('arsip.id_arsip', 'DESC');

        return $builder->get()->getResultArray();
    }

    // -------------------------------------------------------------------------
    // Helper untuk Dashboard (sudah ada, dipertahankan)
    // -------------------------------------------------------------------------

    public function countAllGlobal(): int
    {
        return $this->countAllResults();
    }

    public function countByOpd(int $id_opd): int
    {
        return $this->where('id_opd', $id_opd)->countAllResults();
    }

    public function countByBidang(int $id_bidang): int
    {
        return $this->where('id_bidang', $id_bidang)->countAllResults();
    }
}
