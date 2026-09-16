<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBeritaAcaraDetailTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_berita_acara' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'id_arsip' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id_berita_acara', false);
        $this->forge->addKey('id_arsip', false);
        $this->forge->createTable('berita_acara_detail');
    }

    public function down()
    {
        $this->forge->dropTable('berita_acara_detail');
    }
}
