<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_user' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'id_opd' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'id_bidang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'nip' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'is_active' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
            ],
        ]);

        $this->forge->addKey('id_user', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addForeignKey('id_opd', 'opd', 'id_opd', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('id_bidang', 'bidang', 'id_bidang', 'SET NULL', 'CASCADE');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
