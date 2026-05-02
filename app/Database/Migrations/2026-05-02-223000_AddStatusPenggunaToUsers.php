<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusPenggunaToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'status_pengguna' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'eliminasi'],
                'default'    => 'aktif',
                'after'      => 'status',
            ],
            'tanggal_eliminasi' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'status_pengguna',
            ],
            'dieliminasi_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'tanggal_eliminasi',
            ],
        ]);

        // Default all existing users to 'aktif'
        $db = \Config\Database::connect();
        $db->table('users')->update(['status_pengguna' => 'aktif']);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['status_pengguna', 'tanggal_eliminasi', 'dieliminasi_oleh']);
    }
}
