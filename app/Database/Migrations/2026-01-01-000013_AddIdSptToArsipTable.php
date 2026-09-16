<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityLogTable extends Migration
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
            'module' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false, // create, update, delete
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'old_data' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'new_data' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id', false);
        $this->forge->createTable('activity_log');
    }

    public function down()
    {
        $this->forge->dropTable('activity_log');
    }
}
