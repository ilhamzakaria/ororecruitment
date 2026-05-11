<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityLogAndSessionControl extends Migration
{
    public function up()
    {
        // 1. Table log_aktivitas_tes
        $this->forge->addField([
            'id_log' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_pegawai' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'nama_pegawai' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'nomor_sesi' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'aktivitas' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'detail_aktivitas' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tanggal_aktivitas' => [
                'type' => 'DATE',
            ],
            'jam_aktivitas' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'menit_aktivitas' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'waktu_lengkap' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id_log', true);
        $this->forge->addKey('id_pegawai');
        $this->forge->createTable('log_aktivitas_tes');

        // 2. Table kontrol_sesi_pegawai
        $this->forge->addField([
            'id_kontrol' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_pegawai' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'nama_pegawai' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'nomor_sesi' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'status_sesi' => [
                'type'       => 'ENUM',
                'constraint' => ['belum_dibuka', 'dibuka', 'berjalan', 'selesai'],
                'default'    => 'belum_dibuka',
            ],
            'dibuka_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'role_pembuka' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'tanggal_dibuka' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'waktu_dibuka' => [
                'type' => 'TIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_kontrol', true);
        $this->forge->addKey(['id_pegawai', 'nomor_sesi']);
        $this->forge->createTable('kontrol_sesi_pegawai');

        // 3. Swap urutan_pertanyaan 1 and 2 in pertanyaan_sesi_1
        $db = \Config\Database::connect();
        
        // Use a temporary value to swap
        $db->table('pertanyaan_sesi_1')->where('urutan_pertanyaan', 1)->update(['urutan_pertanyaan' => 999]);
        $db->table('pertanyaan_sesi_1')->where('urutan_pertanyaan', 2)->update(['urutan_pertanyaan' => 1]);
        $db->table('pertanyaan_sesi_1')->where('urutan_pertanyaan', 999)->update(['urutan_pertanyaan' => 2]);
    }

    public function down()
    {
        $this->forge->dropTable('log_aktivitas_tes');
        $this->forge->dropTable('kontrol_sesi_pegawai');
        
        // Restore swap
        $db = \Config\Database::connect();
        $db->table('pertanyaan_sesi_1')->where('urutan_pertanyaan', 1)->update(['urutan_pertanyaan' => 999]);
        $db->table('pertanyaan_sesi_1')->where('urutan_pertanyaan', 2)->update(['urutan_pertanyaan' => 1]);
        $db->table('pertanyaan_sesi_1')->where('urutan_pertanyaan', 999)->update(['urutan_pertanyaan' => 2]);
    }
}
