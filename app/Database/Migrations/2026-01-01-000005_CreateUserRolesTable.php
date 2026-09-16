<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserRolesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_user' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'id_role' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id_user', false);
        $this->forge->addKey('id_role', false);
        $this->forge->createTable('user_roles');
    }

    public function down()
    {
        $this->forge->dropTable('user_roles');
    }
}
