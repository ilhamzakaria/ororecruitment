<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePertanyaanDanJawabanTables extends Migration
{
    public function up()
    {
        // 1. Create pertanyaan_tes table
        $this->forge->addField([
            'id_pertanyaan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'isi_pertanyaan' => [
                'type'       => 'TEXT',
            ],
            'tipe_pertanyaan' => [
                'type'       => 'ENUM',
                'constraint' => ['text', 'angka', 'gambar'],
                'default'    => 'text',
            ],
            'gambar_pertanyaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'pilihan_a' => [
                'type'       => 'TEXT',
            ],
            'pilihan_b' => [
                'type'       => 'TEXT',
            ],
            'pilihan_c' => [
                'type'       => 'TEXT',
            ],
            'pilihan_d' => [
                'type'       => 'TEXT',
            ],
            'jawaban_benar' => [
                'type'       => 'ENUM',
                'constraint' => ['A', 'B', 'C', 'D'],
            ],
            'status_pertanyaan' => [
                'type'       => 'ENUM',
                'constraint' => ['Aktif', 'Nonaktif'],
                'default'    => 'Aktif',
            ],
            'dibuat_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'role_pembuat' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
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
        $this->forge->addKey('id_pertanyaan', true);
        $this->forge->createTable('pertanyaan_tes');

        // 2. Create jawaban_pegawai table
        $this->forge->addField([
            'id_jawaban' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_pertanyaan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_pegawai' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'nama_pegawai' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'jawaban_dipilih' => [
                'type'       => 'ENUM',
                'constraint' => ['A', 'B', 'C', 'D'],
            ],
            'jawaban_benar' => [
                'type'       => 'ENUM',
                'constraint' => ['A', 'B', 'C', 'D'],
            ],
            'status_jawaban' => [
                'type'       => 'ENUM',
                'constraint' => ['Benar', 'Salah'],
            ],
            'nilai' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'tanggal_menjawab' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_jawaban', true);
        $this->forge->createTable('jawaban_pegawai');
    }

    public function down()
    {
        $this->forge->dropTable('jawaban_pegawai');
        $this->forge->dropTable('pertanyaan_tes');
    }
}
