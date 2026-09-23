<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRetensiInaktifToArsip extends Migration
{
    public function up()
    {
        $fields = [
            'tanggal_retensi_inaktif_berakhir' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'tanggal_retensi_aktif_berakhir',
            ],
        ];
        $this->forge->addColumn('arsip', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('arsip', 'tanggal_retensi_inaktif_berakhir');
    }
}
