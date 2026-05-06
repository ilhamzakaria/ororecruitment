<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSessionSettingsAndStatus extends Migration
{
    public function up()
    {
        // 1. Tabel pengaturan_sesi
        $this->forge->addField([
            'id_sesi' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_sesi' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'durasi_menit' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 10,
            ],
            'status_sesi' => [
                'type' => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default' => 'aktif',
            ],
            'tanggal_dibuat' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tanggal_diubah' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_sesi', true);
        $this->forge->createTable('pengaturan_sesi');

        // Insert default data for Sesi 1, 2, 3
        $db = \Config\Database::connect();
        $db->table('pengaturan_sesi')->insertBatch([
            ['nama_sesi' => 'Sesi 1', 'durasi_menit' => 10, 'status_sesi' => 'aktif', 'tanggal_dibuat' => date('Y-m-d H:i:s')],
            ['nama_sesi' => 'Sesi 2', 'durasi_menit' => 10, 'status_sesi' => 'aktif', 'tanggal_dibuat' => date('Y-m-d H:i:s')],
            ['nama_sesi' => 'Sesi 3', 'durasi_menit' => 10, 'status_sesi' => 'aktif', 'tanggal_dibuat' => date('Y-m-d H:i:s')],
        ]);

        // 2. Tabel status_sesi_peserta
        $this->forge->addField([
            'id_status' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_pegawai' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'nomor_sesi' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'waktu_mulai' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'durasi_menit' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 10,
            ],
            'waktu_sisa' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true, // In seconds
            ],
            'status_sesi' => [
                'type' => 'ENUM',
                'constraint' => ['belum_mulai', 'berjalan', 'selesai'],
                'default' => 'belum_mulai',
            ],
            'tanggal_selesai' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_status', true);
        $this->forge->createTable('status_sesi_peserta');
    }

    public function down()
    {
        $this->forge->dropTable('pengaturan_sesi');
        $this->forge->dropTable('status_sesi_peserta');
    }
}
