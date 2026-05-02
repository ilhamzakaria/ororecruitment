<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuthAndMonitoringTables extends Migration
{
    public function up()
    {
        // Pegawai/User table
        $this->forge->addField([
            'id_user' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'nama_lengkap' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'jenis_kelamin' => [
                'type' => 'ENUM',
                'constraint' => ['Laki-laki', 'Perempuan'],
                'null' => true,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['hrd', 'user'],
                'default' => 'user',
            ],
            'posisi_dilamar' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addKey('id_user', true);
        $this->forge->createTable('pegawai', true);

        // Peserta/Sessions table
        $this->forge->addField([
            'session_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'id_user' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'candidate_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'position_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'hrd_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'session_code' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'draft',
            ],
            'current_question' => [
                'type' => 'INT',
                'constraint' => 5,
                'default' => 0,
            ],
            'questions_total' => [
                'type' => 'INT',
                'constraint' => 5,
                'default' => 0,
            ],
            'violations_count' => [
                'type' => 'INT',
                'constraint' => 5,
                'default' => 0,
            ],
            'tab_switches' => [
                'type' => 'INT',
                'constraint' => 5,
                'default' => 0,
            ],
            'is_blocked' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'last_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'started_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'ended_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('session_id', true);
        $this->forge->createTable('peserta', true);

        // Pelanggaran table
        $this->forge->addField([
            'id_pelanggaran' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'session_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'occurred_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id_pelanggaran', true);
        $this->forge->addForeignKey('session_id', 'peserta', 'session_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pelanggaran', true);
    }

    public function down()
    {
        $this->forge->dropTable('pelanggaran');
        $this->forge->dropTable('peserta');
        $this->forge->dropTable('pegawai');
    }
}
