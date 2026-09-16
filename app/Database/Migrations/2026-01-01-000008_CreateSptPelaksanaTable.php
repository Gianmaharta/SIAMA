<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSptPelaksanaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_spt' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'id_user' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'id_bidang' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id_spt', false);
        $this->forge->addKey('id_user', false);
        $this->forge->addKey('id_bidang', false);
        $this->forge->createTable('spt_pelaksana');
    }

    public function down()
    {
        $this->forge->dropTable('spt_pelaksana');
    }
}
