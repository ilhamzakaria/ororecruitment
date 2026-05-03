<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOptionEToPertanyaanTable extends Migration
{
    public function up()
    {
        $fields = [
            'pilihan_e' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'pilihan_d'
            ],
        ];
        $this->forge->addColumn('pertanyaan_tes', $fields);

        // Update ENUM for jawaban_benar
        $this->db->query("ALTER TABLE pertanyaan_tes MODIFY COLUMN jawaban_benar ENUM('A', 'B', 'C', 'D', 'E')");
        $this->db->query("ALTER TABLE jawaban_pegawai MODIFY COLUMN jawaban_dipilih ENUM('A', 'B', 'C', 'D', 'E')");
        $this->db->query("ALTER TABLE jawaban_pegawai MODIFY COLUMN jawaban_benar ENUM('A', 'B', 'C', 'D', 'E')");
    }

    public function down()
    {
        $this->forge->dropColumn('pertanyaan_tes', 'pilihan_e');
        $this->db->query("ALTER TABLE pertanyaan_tes MODIFY COLUMN jawaban_benar ENUM('A', 'B', 'C', 'D')");
        $this->db->query("ALTER TABLE jawaban_pegawai MODIFY COLUMN jawaban_dipilih ENUM('A', 'B', 'C', 'D')");
        $this->db->query("ALTER TABLE jawaban_pegawai MODIFY COLUMN jawaban_benar ENUM('A', 'B', 'C', 'D')");
    }
}
