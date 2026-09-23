<?php

namespace App\Models;

class ArsipModel extends BaseModel
{
    protected $table            = 'arsip';
    protected $primaryKey       = 'id_arsip';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields    = [
        'id_arsip',
        'id_spt',
        'id_opd',
        'id_bidang',
        'id_kode_klasifikasi',
        'nomor_arsip',
        'nama_arsip',
        'kurun_waktu',
        'tingkat_perkembangan',
        'jumlah',
        'kondisi',
        'file_arsip',
        'id_user_upload',
        'status_verifikasi',
        'id_berita_acara',
        'created_by',
        'updated_by',
        'skor_prioritas',
        'status_autentikasi',
        'catatan_penilaian',
        'tanggal_penilaian',
        'id_penilai',
        'tanggal_retensi_aktif_berakhir',
        'status_retensi_aktif',
        'tanggal_retensi_inaktif_berakhir',
        'is_watermarked',
        'watermark_source'
    ];

    protected $validationRules = [
        'nomor_arsip' => 'required|max_length[100]',
        'nama_arsip'  => 'required|max_length[255]',
        'id_opd'      => 'required',
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getArsipWithRelations($id = null, $id_opd = null, $id_bidang = null)
    {
        $builder = $this->db->table('arsip');

        $builder->select([
            'arsip.*',
            'opd.nama_opd',
            'bidang.nama_bidang',
            'kode_klasifikasi.kode AS kode_klasifikasi_text',
            'kode_klasifikasi.nama_klasifikasi',
            'spt.nomor_spt',
            'users.nama AS nama_arsiparis',
        ]);

        $builder->join('opd', 'opd.id_opd = arsip.id_opd', 'left');
        $builder->join('bidang', 'bidang.id_bidang = arsip.id_bidang', 'left');
        $builder->join('kode_klasifikasi', 'kode_klasifikasi.id_kode_klasifikasi = arsip.id_kode_klasifikasi', 'left');
        $builder->join('spt', 'spt.id_spt = arsip.id_spt', 'left');
        $builder->join('users', 'users.id_user = arsip.created_by', 'left');

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

    public function countAllGlobal(): int
    {
        return $this->countAllResults();
    }

    public function countByOpd($id_opd): int
    {
        return $this->where('id_opd', $id_opd)->countAllResults();
    }

    public function countByBidang($id_bidang): int
    {
        return $this->where('id_bidang', $id_bidang)->countAllResults();
    }

    // --- METODE UNTUK PENILAIAN ARSIP ---

    public function getArsipBelumDinilai()
    {
        return $this->where('status_autentikasi', 'Belum Dinilai')->findAll();
    }

    public function getArsipSudahDinilai()
    {
        return $this->where('status_autentikasi !=', 'Belum Dinilai')->findAll();
    }

    public function simpanPenilaian($id_arsip, $dataPenilaian)
    {
        return $this->update($id_arsip, [
            'skor_prioritas'     => $dataPenilaian['skor_prioritas'],
            'status_autentikasi' => $dataPenilaian['status_autentikasi'],
            'catatan_penilaian'  => $dataPenilaian['catatan_penilaian'],
            'tanggal_penilaian'  => date('Y-m-d H:i:s'),
            'id_penilai'         => $dataPenilaian['id_penilai']
        ]);
    }
}
