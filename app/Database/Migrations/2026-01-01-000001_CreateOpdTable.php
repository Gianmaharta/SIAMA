<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOpdTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'kode_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
            ],
            'nama_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'kuota_storage_mb' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1000,
            ],
            'is_active' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
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

        $this->forge->addKey('id_opd', true);
        $this->forge->createTable('opd');
    }

    public function down()
    {
        $this->forge->dropTable('opd');
    }
}
