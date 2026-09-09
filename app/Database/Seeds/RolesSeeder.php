<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id_role' => 1, 'nama_role' => 'Admin_Pemkab'],
            ['id_role' => 2, 'nama_role' => 'Pimpinan'],
            ['id_role' => 3, 'nama_role' => 'Admin_OPD'],
            ['id_role' => 4, 'nama_role' => 'Kepala Bidang'],
            ['id_role' => 5, 'nama_role' => 'Arsiparis'],
        ];

        // Using Simple Queries
        $this->db->table('roles')->insertBatch($data);
    }
}
