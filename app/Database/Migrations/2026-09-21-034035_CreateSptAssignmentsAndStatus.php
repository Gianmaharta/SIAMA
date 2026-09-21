<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSptAssignmentsAndStatus extends Migration
{
    public function up()
    {
        // 1. Tambah status_penugasan ke tabel spt
        $fields = [
            'status_penugasan' => [
                'type'       => 'ENUM',
                'constraint' => ['belum_ditugaskan', 'ditugaskan', 'selesai'],
                'default'    => 'belum_ditugaskan',
                'after'      => 'status'
            ]
        ];
        $this->forge->addColumn('spt', $fields);

        // 2. Buat tabel spt_assignments
        $this->forge->addField([
            'id_assignment' => [
                'type'       => 'VARCHAR',
                'constraint' => '36',
            ],
            'id_spt' => [
                'type'       => 'VARCHAR',
                'constraint' => '36',
            ],
            'id_user' => [ // Arsiparis
                'type'       => 'VARCHAR',
                'constraint' => '36',
            ],
            'assigned_by' => [ // Kabid / Admin OPD
                'type'       => 'VARCHAR',
                'constraint' => '36',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'proses', 'selesai'],
                'default'    => 'pending',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);
        $this->forge->addKey('id_assignment', true);
        $this->forge->addKey('id_spt');
        $this->forge->addKey('id_user');
        $this->forge->addForeignKey('id_spt', 'spt', 'id_spt', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assigned_by', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('spt_assignments');
    }

    public function down()
    {
        $this->forge->dropTable('spt_assignments', true);
        $this->forge->dropColumn('spt', 'status_penugasan');
    }
}
