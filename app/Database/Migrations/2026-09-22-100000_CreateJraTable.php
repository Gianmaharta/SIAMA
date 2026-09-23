<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJraTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_jra' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'id_kode_klasifikasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'retensi_aktif' => [
                'type'       => 'INT',
                'constraint' => 5,
                'null'       => false,
            ],
            'retensi_inaktif' => [
                'type'       => 'INT',
                'constraint' => 5,
                'null'       => false,
            ],
            'keterangan_retensi' => [
                'type'       => 'VARCHAR',
                'constraint' => '100', // Musnah, Permanen, Dinilai Kembali
                'null'       => false,
            ],
            'klasifikasi_keamanan' => [
                'type'       => 'VARCHAR',
                'constraint' => '100', // Biasa, Rahasia, Sangat Rahasia
                'null'       => true,
            ],
            'hak_akses' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'akses_publik' => [
                'type'       => 'VARCHAR',
                'constraint' => '100', // Terbuka, Tertutup
                'null'       => true,
            ],
            'unit_pengolah' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
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
        ]);

        $this->forge->addKey('id_jra', true);
        // Only adding foreign key if it's strictly needed, CI4 can do it:
        $this->forge->addForeignKey('id_kode_klasifikasi', 'kode_klasifikasi', 'id_kode_klasifikasi', 'CASCADE', 'CASCADE');
        $this->forge->createTable('jra');
    }

    public function down()
    {
        $this->forge->dropTable('jra');
    }
}
