<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SiamaSeeder extends Seeder
{
    public function run()
    {
        // ============================================================
        // 1. SEED TABEL: opd & bidang (Untuk referensi Foreign Key)
        // ============================================================
        $opd = [
            ['id_opd' => 1, 'kode_opd' => 'OPD-001', 'nama_opd' => 'Dinas Komunikasi dan Informatika'],
        ];
        $this->db->table('opd')->ignore(true)->insertBatch($opd);

        $bidang = [
            ['id_bidang' => 1, 'id_opd' => 1, 'nama_bidang' => 'Bidang Pengelolaan Arsip Dinamis'],
        ];
        $this->db->table('bidang')->ignore(true)->insertBatch($bidang);

        // ============================================================
        // 2. SEED TABEL: roles
        // ============================================================
        $roles = [
            ['id_role' => 1, 'nama_role' => 'Admin_Pemkab'],
            ['id_role' => 2, 'nama_role' => 'Pimpinan'],
            ['id_role' => 3, 'nama_role' => 'Admin_OPD'],
            ['id_role' => 4, 'nama_role' => 'Kepala_Bidang'],
            ['id_role' => 5, 'nama_role' => 'Arsiparis'],
        ];

        // Mencegah duplicate entry jika seeder dijalankan ulang
        $this->db->table('roles')->ignore(true)->insertBatch($roles);
        
        // ============================================================
        // 3. SEED TABEL: users
        // ============================================================
        $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);

        $users = [
            [
                'id_user'   => 1,
                'email'     => 'admin.pemkab@siama.test',
                'password'  => $hashedPassword,
                'nama'      => 'Administrator Pemkab',
                'id_opd'    => null,
                'id_bidang' => null,
                'is_active' => 1,
            ],
            [
                'id_user'   => 2,
                'email'     => 'pimpinan@siama.test',
                'password'  => $hashedPassword,
                'nama'      => 'Kepala Dinas (Pimpinan)',
                'id_opd'    => 1,
                'id_bidang' => null,
                'is_active' => 1,
            ],
            [
                'id_user'   => 3,
                'email'     => 'admin.opd@siama.test',
                'password'  => $hashedPassword,
                'nama'      => 'Administrator OPD',
                'id_opd'    => 1,
                'id_bidang' => null,
                'is_active' => 1,
            ],
            [
                'id_user'   => 4,
                'email'     => 'kabid@siama.test',
                'password'  => $hashedPassword,
                'nama'      => 'Kepala Bidang',
                'id_opd'    => 1,
                'id_bidang' => 1,
                'is_active' => 1,
            ],
            [
                'id_user'   => 5,
                'email'     => 'arsiparis@siama.test',
                'password'  => $hashedPassword,
                'nama'      => 'Budi Santoso (Arsiparis)',
                'id_opd'    => 1,
                'id_bidang' => 1,
                'is_active' => 1,
            ],
        ];

        // Mencegah duplicate entry jika seeder dijalankan ulang
        $this->db->table('users')->ignore(true)->insertBatch($users);

        // ============================================================
        // 3. SEED TABEL: user_roles
        // ============================================================
        $userRoles = [
            ['id_user' => 1, 'id_role' => 1], // admin.pemkab   -> Admin_Pemkab
            ['id_user' => 2, 'id_role' => 2], // pimpinan       -> Pimpinan
            ['id_user' => 3, 'id_role' => 3], // admin.opd      -> Admin_OPD
            ['id_user' => 4, 'id_role' => 4], // kabid          -> Kepala_Bidang
            ['id_user' => 5, 'id_role' => 5], // arsiparis      -> Arsiparis
        ];

        // Mencegah duplicate entry jika seeder dijalankan ulang
        $this->db->table('user_roles')->ignore(true)->insertBatch($userRoles);
    }
}
