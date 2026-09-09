<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKodeKlasifikasiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_klasifikasi' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
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
            'retensi_aktif' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'retensi_inaktif' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id_klasifikasi', true);
        $this->forge->createTable('kode_klasifikasi');
    }

    public function down()
    {
        $this->forge->dropTable('kode_klasifikasi');
    }
}
