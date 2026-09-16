<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateArsipTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_arsip' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'id_spt' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'id_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'id_bidang' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'id_kode_klasifikasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
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
            'kurun_waktu' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'tingkat_perkembangan' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'kondisi' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'file_arsip' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'id_user_upload' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'status_verifikasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Menunggu',
            ],
            'id_berita_acara' => [
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

        $this->forge->addKey('id_arsip', true);
        $this->forge->addKey('id_spt', false);
        $this->forge->addKey('id_opd', false);
        $this->forge->addKey('id_bidang', false);
        $this->forge->addKey('id_kode_klasifikasi', false);
        $this->forge->addKey('id_user_upload', false);
        $this->forge->addKey('id_berita_acara', false);
        $this->forge->createTable('arsip');
        
        // Add FULLTEXT index separately since CodeIgniter's forge doesn't natively support fulltext easily in all drivers via addKey
        // However, this is MySQL specific.
        $db = \Config\Database::connect();
        if ($db->DBDriver === 'MySQLi') {
            $db->query('ALTER TABLE arsip ADD FULLTEXT INDEX search_index (nomor_arsip, nama_arsip, kurun_waktu)');
        }
    }

    public function down()
    {
        $this->forge->dropTable('arsip');
    }
}
