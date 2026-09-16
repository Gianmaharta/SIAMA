<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccessLogTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
                'null'       => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'event' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false, // login, logout, failed_login
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id', false);
        $this->forge->createTable('access_log');
    }

    public function down()
    {
        $this->forge->dropTable('access_log');
    }
}
