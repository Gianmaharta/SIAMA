<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSptPelaksanaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_spt' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'id_bidang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
        ]);

        $this->forge->addKey(['id_spt', 'id_user'], true);
        $this->forge->addForeignKey('id_spt', 'spt', 'id_spt', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_bidang', 'bidang', 'id_bidang', 'CASCADE', 'CASCADE');
        $this->forge->createTable('spt_pelaksana');
    }

    public function down()
    {
        $this->forge->dropTable('spt_pelaksana');
    }
}
