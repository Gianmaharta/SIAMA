<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNotificationEnhancements extends Migration
{
    public function up()
    {
        $fieldsToAdd = [];

        if (!$this->db->fieldExists('type', 'notifications')) {
            $fieldsToAdd['type'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'message'
            ];
        }

        if (!$this->db->fieldExists('reference_id', 'notifications')) {
            $fieldsToAdd['reference_id'] = [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'after'      => 'type'
            ];
        }

        if (!$this->db->fieldExists('reference_type', 'notifications')) {
            $fieldsToAdd['reference_type'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'reference_id'
            ];
        }

        if (!$this->db->fieldExists('url', 'notifications')) {
            $fieldsToAdd['url'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'reference_type'
            ];
        }

        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('notifications', $fieldsToAdd);
        }
    }

    public function down()
    {
        $fieldsToDrop = [];

        foreach (['url', 'reference_type', 'reference_id', 'type'] as $col) {
            if ($this->db->fieldExists($col, 'notifications')) {
                $fieldsToDrop[] = $col;
            }
        }

        if (!empty($fieldsToDrop)) {
            $this->forge->dropColumn('notifications', $fieldsToDrop);
        }
    }
}

