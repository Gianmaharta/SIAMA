<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Tambah kolom id_spt (nullable FK) ke tabel arsip.
 * Kolom ini digunakan untuk menghubungkan arsip dengan Surat Perintah Tugas (SPT)
 * yang mendasari kegiatan alih media. Bersifat opsional (nullable).
 */
class AddIdSptToArsipTable extends Migration
{
    public function up()
    {
        $fields = [
            'id_spt' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'id_arsip',
            ],
        ];

        $this->forge->addColumn('arsip', $fields);

        // Tambahkan Foreign Key ke tabel spt via raw query
        // (CI4 forge tidak mendukung addForeignKey setelah addColumn dalam satu operasi)
        $this->db->query(
            'ALTER TABLE `arsip` ADD CONSTRAINT `fk_arsip_id_spt` 
             FOREIGN KEY (`id_spt`) REFERENCES `spt`(`id_spt`) 
             ON DELETE SET NULL ON UPDATE CASCADE'
        );
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `arsip` DROP FOREIGN KEY `fk_arsip_id_spt`');
        $this->forge->dropColumn('arsip', 'id_spt');
    }
}
