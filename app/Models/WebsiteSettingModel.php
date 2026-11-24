<?php

namespace App\Models;

use CodeIgniter\Model;

class WebsiteSettingModel extends Model
{
    protected $table            = 'website_settings';
    protected $primaryKey       = 'id_setting';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'setting_key',
        'setting_value',
        'updated_at',
    ];

    protected static ?array $cachedSettings = null;

    public static function getDefaults(): array
    {
        return [
            'site_name'         => 'SIAMA',
            'site_tagline'      => 'Sistem Informasi Alih Media Arsip',
            'instansi_name'     => 'Pemerintah Kabupaten Buleleng',
            'site_description'  => 'Platform terpadu digitalisasi, autentikasi watermark, verifikasi berjenjang, dan manajemen Jadwal Retensi Arsip (JRA) dinamis di lingkungan Pemerintah Kabupaten Buleleng.',
            'site_logo'         => 'logo siama1.png',
            'contact_address'   => 'Jl. Pahlawan No. 1, Singaraja, Kabupaten Buleleng, Bali 81113',
            'contact_phone'     => '(0362) 21985',
            'contact_email'     => 'kominfosanti@bulelengkab.go.id',
            'social_instagram'  => 'https://instagram.com/pemkabbuleleng',
            'social_facebook'   => 'https://facebook.com/pemkabbuleleng',
            'social_youtube'    => 'https://youtube.com/@pemkabbuleleng',
            'social_website'    => 'https://bulelengkab.go.id',
            'login_mode'        => 'both',
            'default_password'  => 'Admin123!',
            'password_mode'     => 'default',
        ];
    }

    public function getAllMap(bool $forceRefresh = false): array
    {
        if (self::$cachedSettings !== null && !$forceRefresh) {
            return self::$cachedSettings;
        }

        $settings = self::getDefaults();
        try {
            if ($this->db->tableExists($this->table)) {
                $rows = $this->findAll();
                foreach ($rows as $row) {
                    $settings[$row['setting_key']] = (string) ($row['setting_value'] ?? '');
                }
            }
        } catch (\Throwable $e) {
            // Fallback ke default jika tabel belum dimigrasi
        }

        self::$cachedSettings = $settings;
        return $settings;
    }

    public function getValue(string $key, ?string $default = null): string
    {
        $map = $this->getAllMap();
        if (array_key_exists($key, $map) && $map[$key] !== '') {
            return (string) $map[$key];
        }
        $defaults = self::getDefaults();
        return $default ?? ($defaults[$key] ?? '');
    }

    public function saveSettings(array $data): void
    {
        if (!$this->db->tableExists($this->table)) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        foreach ($data as $key => $value) {
            $existing = $this->where('setting_key', $key)->first();
            if ($existing) {
                $this->update($existing['id_setting'], [
                    'setting_value' => (string) $value,
                    'updated_at'    => $now,
                ]);
            } else {
                $this->insert([
                    'setting_key'   => (string) $key,
                    'setting_value' => (string) $value,
                    'updated_at'    => $now,
                ]);
            }
        }

        self::$cachedSettings = null;
    }
}
