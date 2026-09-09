<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBeritaAcaraTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_berita_acara' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'id_spt' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'nomor_ba' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'tanggal_ba' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'id_pelaksana' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'id_kabid' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'id_pimpinan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'status_persetujuan' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Draf',
            ],
            'catatan_revisi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'file_ba_pdf' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id_berita_acara', true);
        $this->forge->addForeignKey('id_spt', 'spt', 'id_spt', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_pelaksana', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_kabid', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_pimpinan', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('berita_acara');
    }

    public function down()
    {
        $this->forge->dropTable('berita_acara');
    }
}
