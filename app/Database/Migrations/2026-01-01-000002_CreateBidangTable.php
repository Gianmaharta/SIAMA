<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBidangTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_bidang' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'id_opd' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'nama_bidang' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id_bidang', true);
        $this->forge->addForeignKey('id_opd', 'opd', 'id_opd', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bidang');
    }

    public function down()
    {
        $this->forge->dropTable('bidang');
    }
}
