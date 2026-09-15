<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSptTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_spt' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'id_opd' => [
                'type'       => 'INT',
                'constraint' => 11,
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
                'type'       => 'INT',
                'constraint' => 11,
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
        ]);

        $this->forge->addKey('id_spt', true);
        $this->forge->addForeignKey('id_opd', 'opd', 'id_opd', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_pimpinan', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('spt');
    }

    public function down()
    {
        $this->forge->dropTable('spt');
    }
}
