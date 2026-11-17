<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSenderIdToNotifications extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('sender_id', 'notifications')) {
            $this->forge->addColumn('notifications', [
                'sender_id' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 36,
                    'null'       => true,
                    'after'      => 'id_user'
                ]
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('sender_id', 'notifications')) {
            $this->forge->dropColumn('notifications', 'sender_id');
        }
    }
}
