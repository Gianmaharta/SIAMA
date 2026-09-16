<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSptTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_spt' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'id_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'nomor_spt' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'tanggal_spt' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'id_pimpinan' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'file_spt' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'perihal' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'tanggal_mulai' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'tanggal_selesai' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Aktif',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'updated_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id_spt', true);
        $this->forge->addKey('id_opd', false);
        $this->forge->addKey('id_pimpinan', false);
        $this->forge->createTable('spt');
    }

    public function down()
    {
        $this->forge->dropTable('spt');
    }
}
