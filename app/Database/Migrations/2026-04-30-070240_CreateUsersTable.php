<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        // 1. Create users table
        $this->forge->addField([
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['hrd', 'pegawai'],
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'aktif',
            ],
            'session_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');

        // 2. Transfer data from pegawai
        $db = \Config\Database::connect();
        $pegawai = $db->table('pegawai')->get()->getResultArray();
        foreach ($pegawai as $p) {
            $db->table('users')->insert([
                'id'         => $p['id_user'],
                'username'   => $p['username'],
                'password'   => $p['password'],
                'role'       => 'pegawai',
                'status'     => $p['status'] ?? 'aktif',
                'session_id' => $p['session_id'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // 3. Transfer data from hrd
        $hrd = $db->table('hrd')->get()->getResultArray();
        foreach ($hrd as $h) {
            $db->table('users')->insert([
                'id'         => $h['id_hrd'],
                'username'   => $h['username'],
                'password'   => $h['password'],
                'role'       => 'hrd',
                'status'     => $h['status'] ?? 'aktif',
                'session_id' => $h['session_id'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // 4. Drop columns from original tables
        $this->forge->dropColumn('pegawai', ['username', 'password', 'status', 'session_id']);
        $this->forge->dropColumn('hrd', ['username', 'password', 'status', 'session_id']);
    }

    public function down()
    {
        // Re-add columns to pegawai
        $this->forge->addColumn('pegawai', [
            'username'   => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true, 'null' => true],
            'password'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'     => ['type' => 'ENUM', 'constraint' => ['aktif', 'nonaktif'], 'default' => 'nonaktif', 'null' => true],
            'session_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);

        // Re-add columns to hrd
        $this->forge->addColumn('hrd', [
            'username'   => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true, 'null' => true],
            'password'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'     => ['type' => 'ENUM', 'constraint' => ['aktif', 'nonaktif'], 'default' => 'aktif', 'null' => true],
            'session_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);

        // Restore data would be complex, but for simplicity:
        $db = \Config\Database::connect();
        $users = $db->table('users')->get()->getResultArray();
        foreach ($users as $u) {
            if ($u['role'] === 'pegawai') {
                $db->table('pegawai')->where('id_user', $u['id'])->update([
                    'username'   => $u['username'],
                    'password'   => $u['password'],
                    'status'     => $u['status'],
                    'session_id' => $u['session_id'],
                ]);
            } else {
                $db->table('hrd')->where('id_hrd', $u['id'])->update([
                    'username'   => $u['username'],
                    'password'   => $u['password'],
                    'status'     => $u['status'],
                    'session_id' => $u['session_id'],
                ]);
            }
        }

        $this->forge->dropTable('users');
    }
}
