<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOptionsFToH extends Migration
{
    public function up()
    {
        $fields = [
            'pilihan_f' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'gambar_pilihan_e',
            ],
            'tipe_pilihan_f' => [
                'type' => 'ENUM',
                'constraint' => ['text', 'gambar'],
                'default' => 'text',
                'after' => 'pilihan_f',
            ],
            'gambar_pilihan_f' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'tipe_pilihan_f',
            ],
            'pilihan_g' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'gambar_pilihan_f',
            ],
            'tipe_pilihan_g' => [
                'type' => 'ENUM',
                'constraint' => ['text', 'gambar'],
                'default' => 'text',
                'after' => 'pilihan_g',
            ],
            'gambar_pilihan_g' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'tipe_pilihan_g',
            ],
            'pilihan_h' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'gambar_pilihan_g',
            ],
            'tipe_pilihan_h' => [
                'type' => 'ENUM',
                'constraint' => ['text', 'gambar'],
                'default' => 'text',
                'after' => 'pilihan_h',
            ],
            'gambar_pilihan_h' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'tipe_pilihan_h',
            ],
        ];

        $this->forge->addColumn('pertanyaan_tes', $fields);

        $this->db->query("ALTER TABLE pertanyaan_tes MODIFY COLUMN jawaban_benar ENUM('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H')");
        $this->db->query("ALTER TABLE jawaban_pegawai MODIFY COLUMN jawaban_dipilih ENUM('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H')");
        $this->db->query("ALTER TABLE jawaban_pegawai MODIFY COLUMN jawaban_benar ENUM('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H')");
    }

    public function down()
    {
        $this->forge->dropColumn('pertanyaan_tes', [
            'pilihan_f', 'tipe_pilihan_f', 'gambar_pilihan_f',
            'pilihan_g', 'tipe_pilihan_g', 'gambar_pilihan_g',
            'pilihan_h', 'tipe_pilihan_h', 'gambar_pilihan_h',
        ]);

        $this->db->query("ALTER TABLE pertanyaan_tes MODIFY COLUMN jawaban_benar ENUM('A', 'B', 'C', 'D', 'E')");
        $this->db->query("ALTER TABLE jawaban_pegawai MODIFY COLUMN jawaban_dipilih ENUM('A', 'B', 'C', 'D', 'E')");
        $this->db->query("ALTER TABLE jawaban_pegawai MODIFY COLUMN jawaban_benar ENUM('A', 'B', 'C', 'D', 'E')");
    }
}
