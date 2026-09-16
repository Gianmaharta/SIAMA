<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBidangTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_bidang' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'id_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'nama_bidang' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
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

        $this->forge->addKey('id_bidang', true);
        $this->forge->addKey('id_opd', false);
        $this->forge->createTable('bidang');
    }

    public function down()
    {
        $this->forge->dropTable('bidang');
    }
}
