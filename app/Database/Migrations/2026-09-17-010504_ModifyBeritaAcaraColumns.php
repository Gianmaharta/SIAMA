<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyBeritaAcaraColumns extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'name'       => 'status_persetujuan',
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Draf',
            ],
            'id_pembuat' => [
                'name'       => 'id_pelaksana',
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'id_verifikator' => [
                'name'       => 'id_kabid',
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'file_ba' => [
                'name'       => 'file_ba_pdf',
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
        ];

        $this->forge->modifyColumn('berita_acara', $fields);

        // Menambahkan kolom baru
        $this->forge->addColumn('berita_acara', [
            'catatan_revisi' => [
                'type' => 'TEXT',
                'null' => true,
            ]
        ]);
    }

    public function down()
    {
        // Revert columns
        $fields = [
            'status_persetujuan' => [
                'name'       => 'status',
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Draf',
            ],
            'id_pelaksana' => [
                'name'       => 'id_pembuat',
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'id_kabid' => [
                'name'       => 'id_verifikator',
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'file_ba_pdf' => [
                'name'       => 'file_ba',
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
        ];

        $this->forge->modifyColumn('berita_acara', $fields);
        $this->forge->dropColumn('berita_acara', 'catatan_revisi');
    }
}
