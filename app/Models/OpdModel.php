<?php

namespace App\Models;

use App\Models\BaseModel;

class OpdModel extends BaseModel
{
    protected $table            = 'opd';
    protected $primaryKey       = 'id_opd';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_opd', 'kode_opd', 'nama_opd', 'kuota_storage_mb', 'is_active', 'created_by', 'updated_by'];

    protected $beforeInsert = ['generateUuid', 'setCreatedBy', 'ensureKodeOpd'];

    // Validasi
    protected $validationRules      = [
        'kode_opd' => 'permit_empty|max_length[50]',
        'nama_opd' => 'required|max_length[255]'
    ];
    protected $validationMessages   = [
        'nama_opd' => [
            'required' => 'Nama OPD harus diisi.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Pastikan kode_opd selalu terisi otomatis sebelum insert
     */
    protected function ensureKodeOpd(array $data)
    {
        if (empty($data['data']['kode_opd'])) {
            $data['data']['kode_opd'] = $this->generateNextKodeOpd();
        }
        return $data;
    }

    /**
     * Generate Kode OPD otomatis berurutan (misal: OPD-001, OPD-002, ..., OPD-023)
     */
    public function generateNextKodeOpd(): string
    {
        $opds = $this->select('kode_opd')->findAll();
        $maxNum = 0;
        foreach ($opds as $item) {
            $code = $item['kode_opd'] ?? '';
            if (preg_match('/^OPD-(\d+)$/i', $code, $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }
        $nextNum = $maxNum + 1;
        return sprintf('OPD-%03d', $nextNum);
    }

    /**
     * Sinkronisasi data dari API Eksternal
     */
    public function syncFromApi($apiData)
    {
        foreach ($apiData as $data) {
            $existing = $this->where('kode_opd', $data['kode_opd'])->first();
            if ($existing) {
                $this->update($existing['id_opd'], $data);
            } else {
                $this->insert($data);
            }
        }
    }
}
