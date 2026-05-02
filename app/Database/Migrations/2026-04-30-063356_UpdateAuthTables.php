<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAuthTables extends Migration
{
    public function up()
    {
        // Add status and session_id to pegawai table
        $this->forge->addColumn('pegawai', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'nonaktif',
                'after'      => 'password',
            ],
            'session_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'status',
            ],
        ]);

        // Add status and session_id to hrd table
        $this->forge->addColumn('hrd', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'aktif',
                'after'      => 'password',
            ],
            'session_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'status',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pegawai', ['status', 'session_id']);
        $this->forge->dropColumn('hrd', ['status', 'session_id']);
    }
}
