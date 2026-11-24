<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWebsiteSettingsAndSuperAdmin extends Migration
{
    private function uuidv4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function up()
    {
        if (!$this->db->tableExists('website_settings')) {
            $this->forge->addField([
                'id_setting' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'setting_key' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'setting_value' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id_setting', true);
            $this->forge->addUniqueKey('setting_key');
            $this->forge->createTable('website_settings', true);
        }

        $now = date('Y-m-d H:i:s');
        $defaults = [
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

        foreach ($defaults as $key => $val) {
            $exists = $this->db->table('website_settings')->where('setting_key', $key)->countAllResults();
            if ($exists === 0) {
                $this->db->table('website_settings')->insert([
                    'setting_key'   => $key,
                    'setting_value' => $val,
                    'updated_at'    => $now,
                ]);
            }
        }

        // Pastikan role Super_Admin tersedia
        if ($this->db->tableExists('roles')) {
            $superRole = $this->db->table('roles')->where('nama_role', 'Super_Admin')->get()->getRowArray();
            if (!$superRole) {
                $idRoleSuper = $this->uuidv4();
                $this->db->table('roles')->insert([
                    'id_role'   => $idRoleSuper,
                    'nama_role' => 'Super_Admin',
                ]);
            } else {
                $idRoleSuper = $superRole['id_role'];
            }

            // Pastikan akun default Super Admin tersedia
            if ($this->db->tableExists('users') && $this->db->tableExists('user_roles')) {
                $superUser = $this->db->table('users')->where('email', 'superadmin@siama.test')->get()->getRowArray();
                if (!$superUser) {
                    $idUserSuper = $this->uuidv4();
                    $this->db->table('users')->insert([
                        'id_user'             => $idUserSuper,
                        'email'               => 'superadmin@siama.test',
                        'password'            => password_hash('Admin123!', PASSWORD_DEFAULT),
                        'nama'                => 'Super Administrator',
                        'id_opd'              => null,
                        'id_bidang'           => null,
                        'is_active'           => 1,
                        'is_default_password' => 0,
                        'created_at'          => $now,
                    ]);
                    $this->db->table('user_roles')->insert([
                        'id_user' => $idUserSuper,
                        'id_role' => $idRoleSuper,
                    ]);
                }
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('website_settings', true);
    }
}
