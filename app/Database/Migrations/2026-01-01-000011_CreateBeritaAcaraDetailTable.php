<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBeritaAcaraDetailTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_berita_acara' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'id_arsip' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
        ]);

        $this->forge->addKey(['id_berita_acara', 'id_arsip'], true);
        $this->forge->addForeignKey('id_berita_acara', 'berita_acara', 'id_berita_acara', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_arsip', 'arsip', 'id_arsip', 'CASCADE', 'CASCADE');
        $this->forge->createTable('berita_acara_detail');
    }

    public function down()
    {
        $this->forge->dropTable('berita_acara_detail');
    }
}
