<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSimpegColumnsToOpdAndBidang extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('opd')) {
            if (!$this->db->fieldExists('nama_jenis', 'opd')) {
                $this->forge->addColumn('opd', [
                    'nama_jenis' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 50,
                        'null'       => true,
                        'after'      => 'nama_opd',
                    ],
                ]);
            }
            if (!$this->db->fieldExists('alamat', 'opd')) {
                $this->forge->addColumn('opd', [
                    'alamat' => [
                        'type'  => 'TEXT',
                        'null'  => true,
                        'after' => 'nama_jenis',
                    ],
                ]);
            }
        }

        if ($this->db->tableExists('bidang')) {
            if (!$this->db->fieldExists('kode_bidang', 'bidang')) {
                $this->forge->addColumn('bidang', [
                    'kode_bidang' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 50,
                        'null'       => true,
                        'after'      => 'id_opd',
                    ],
                ]);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('opd')) {
            if ($this->db->fieldExists('alamat', 'opd')) {
                $this->forge->dropColumn('opd', 'alamat');
            }
            if ($this->db->fieldExists('nama_jenis', 'opd')) {
                $this->forge->dropColumn('opd', 'nama_jenis');
            }
        }
        if ($this->db->tableExists('bidang') && $this->db->fieldExists('kode_bidang', 'bidang')) {
            $this->forge->dropColumn('bidang', 'kode_bidang');
        }
    }
}
