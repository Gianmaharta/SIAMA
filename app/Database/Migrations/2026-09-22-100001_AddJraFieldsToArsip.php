<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJraFieldsToArsip extends Migration
{
    public function up()
    {
        $this->forge->addColumn('arsip', [
            'tanggal_retensi_aktif_berakhir' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status_retensi_aktif' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Aktif', // Aktif, Inaktif, Musnah, Permanen
            ],
            'is_watermarked' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'watermark_source' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'none', // none, system, offline
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('arsip', ['tanggal_retensi_aktif_berakhir', 'status_retensi_aktif', 'is_watermarked', 'watermark_source']);
    }
}
