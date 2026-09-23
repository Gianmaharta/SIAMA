<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDasarPertimbanganToJra extends Migration
{
    public function up()
    {
        $this->forge->addColumn('jra', [
            'dasar_pertimbangan' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'akses_publik'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('jra', 'dasar_pertimbangan');
    }
}
