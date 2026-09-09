<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateArsipTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_arsip' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'id_opd' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'id_bidang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'id_klasifikasi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'nomor_arsip' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'nama_arsip' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'tahun_penciptaan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'kategori_jra' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
            ],
            'kondisi_fisik' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
            ],
            'metode_alih_media' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
            ],
            'skor_prioritas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'file_digital' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'status_autentikasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Belum Watermark',
            ],
            'status_alih_media' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Belum Diajukan',
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id_arsip', true);
        $this->forge->addForeignKey('id_opd', 'opd', 'id_opd', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_bidang', 'bidang', 'id_bidang', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_klasifikasi', 'kode_klasifikasi', 'id_klasifikasi', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('arsip');
    }

    public function down()
    {
        $this->forge->dropTable('arsip');
    }
}
