<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddManagerRole extends Migration
{
    public function up()
    {
        // 1. Update role enum in users table
        // MySQL specific: modify column
        $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('hrd', 'pegawai', 'manager')");

        // 2. Create manager table
        $this->forge->addField([
            'id_manager' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);
        $this->forge->addKey('id_manager', true);
        $this->forge->createTable('manager');

        // 3. Add an initial manager for testing
        $password = password_hash('manager123', PASSWORD_DEFAULT);
        $this->db->table('users')->insert([
            'id'         => 'MGR001',
            'username'   => 'manager001',
            'password'   => $password,
            'role'       => 'manager',
            'status'     => 'aktif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->db->table('manager')->insert([
            'id_manager' => 'MGR001',
            'nama'       => 'Super Manager',
        ]);
    }

    public function down()
    {
        $this->db->table('users')->where('id', 'MGR001')->delete();
        $this->forge->dropTable('manager');
        $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('hrd', 'pegawai')");
    }
}
