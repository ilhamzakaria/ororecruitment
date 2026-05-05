<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTripleSesiTables extends Migration
{
    public function up()
    {
        $sessionCount = 3;

        for ($i = 1; $i <= $sessionCount; $i++) {
            // 1. Create pertanyaan_sesi_X
            $this->forge->addField([
                'id_pertanyaan' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'urutan_pertanyaan' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'tipe_pertanyaan' => [
                    'type'       => 'ENUM',
                    'constraint' => ['text', 'angka', 'gambar'],
                    'default'    => 'text',
                ],
                'isi_pertanyaan' => [
                    'type'       => 'TEXT',
                ],
                'gambar_pertanyaan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'pilihan_a' => ['type' => 'TEXT', 'null' => true],
                'pilihan_b' => ['type' => 'TEXT', 'null' => true],
                'pilihan_c' => ['type' => 'TEXT', 'null' => true],
                'pilihan_d' => ['type' => 'TEXT', 'null' => true],
                'pilihan_e' => ['type' => 'TEXT', 'null' => true],
                'gambar_pilihan_a' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'gambar_pilihan_b' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'gambar_pilihan_c' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'gambar_pilihan_d' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'gambar_pilihan_e' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'tipe_pilihan_a'   => ['type' => 'ENUM', 'constraint' => ['text', 'gambar'], 'default' => 'text'],
                'tipe_pilihan_b'   => ['type' => 'ENUM', 'constraint' => ['text', 'gambar'], 'default' => 'text'],
                'tipe_pilihan_c'   => ['type' => 'ENUM', 'constraint' => ['text', 'gambar'], 'default' => 'text'],
                'tipe_pilihan_d'   => ['type' => 'ENUM', 'constraint' => ['text', 'gambar'], 'default' => 'text'],
                'tipe_pilihan_e'   => ['type' => 'ENUM', 'constraint' => ['text', 'gambar'], 'default' => 'text'],
                'jawaban_benar' => [
                    'type'       => 'ENUM',
                    'constraint' => ['A', 'B', 'C', 'D', 'E'],
                ],
                'status_pertanyaan' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Aktif', 'Nonaktif'],
                    'default'    => 'Aktif',
                ],
                'tanggal_dibuat' => ['type' => 'DATETIME', 'null' => true],
                'tanggal_diubah' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id_pertanyaan', true);
            $this->forge->createTable("pertanyaan_sesi_$i");

            // 2. Create jawaban_sesi_X
            $this->forge->addField([
                'id_jawaban' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'id_pegawai' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                ],
                'nama_pegawai' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'id_pertanyaan' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'nomor_pertanyaan' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                ],
                'jawaban_pegawai' => [
                    'type'       => 'ENUM',
                    'constraint' => ['A', 'B', 'C', 'D', 'E'],
                ],
                'jawaban_benar' => [
                    'type'       => 'ENUM',
                    'constraint' => ['A', 'B', 'C', 'D', 'E'],
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
                'tanggal_menjawab' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id_jawaban', true);
            $this->forge->createTable("jawaban_sesi_$i");
        }
    }

    public function down()
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->forge->dropTable("jawaban_sesi_$i");
            $this->forge->dropTable("pertanyaan_sesi_$i");
        }
    }
}
