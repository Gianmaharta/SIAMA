<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKodeKlasifikasiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_kode_klasifikasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
            ],
            'nama_klasifikasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
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

        $this->forge->addKey('id_kode_klasifikasi', true);
        $this->forge->createTable('kode_klasifikasi');
    }

    public function down()
    {
        $this->forge->dropTable('kode_klasifikasi');
    }
}
