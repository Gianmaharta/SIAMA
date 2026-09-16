<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBeritaAcaraTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_berita_acara' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'id_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'id_spt' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
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
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Draf',
            ],
            'file_ba' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'id_pembuat' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'id_verifikator' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'id_pimpinan' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
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

        $this->forge->addKey('id_berita_acara', true);
        $this->forge->addKey('id_opd', false);
        $this->forge->addKey('id_spt', false);
        $this->forge->addKey('id_pembuat', false);
        $this->forge->addKey('id_verifikator', false);
        $this->forge->addKey('id_pimpinan', false);
        $this->forge->createTable('berita_acara');
    }

    public function down()
    {
        $this->forge->dropTable('berita_acara');
    }
}
