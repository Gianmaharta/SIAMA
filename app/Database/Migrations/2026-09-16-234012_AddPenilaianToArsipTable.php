<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPenilaianToArsipTable extends Migration
{
    public function up()
    {
        $fields = [
            'skor_prioritas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'status_autentikasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Belum Dinilai',
            ],
            'catatan_penilaian' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tanggal_penilaian' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'id_penilai' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
        ];

        $this->forge->addColumn('arsip', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('arsip', ['skor_prioritas', 'status_autentikasi', 'catatan_penilaian', 'tanggal_penilaian', 'id_penilai']);
    }
}
