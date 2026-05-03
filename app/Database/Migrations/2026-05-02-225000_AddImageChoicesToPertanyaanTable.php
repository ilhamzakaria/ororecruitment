<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImageChoicesToPertanyaanTable extends Migration
{
    public function up()
    {
        $fields = [
            'tipe_pilihan_a'   => ['type' => 'ENUM', 'constraint' => ['text', 'gambar'], 'default' => 'text', 'after' => 'pilihan_a'],
            'gambar_pilihan_a' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'tipe_pilihan_a'],
            
            'tipe_pilihan_b'   => ['type' => 'ENUM', 'constraint' => ['text', 'gambar'], 'default' => 'text', 'after' => 'pilihan_b'],
            'gambar_pilihan_b' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'tipe_pilihan_b'],
            
            'tipe_pilihan_c'   => ['type' => 'ENUM', 'constraint' => ['text', 'gambar'], 'default' => 'text', 'after' => 'pilihan_c'],
            'gambar_pilihan_c' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'tipe_pilihan_c'],
            
            'tipe_pilihan_d'   => ['type' => 'ENUM', 'constraint' => ['text', 'gambar'], 'default' => 'text', 'after' => 'pilihan_d'],
            'gambar_pilihan_d' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'tipe_pilihan_d'],
            
            'tipe_pilihan_e'   => ['type' => 'ENUM', 'constraint' => ['text', 'gambar'], 'default' => 'text', 'after' => 'pilihan_e'],
            'gambar_pilihan_e' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'tipe_pilihan_e'],
        ];
        $this->forge->addColumn('pertanyaan_tes', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pertanyaan_tes', [
            'tipe_pilihan_a', 'gambar_pilihan_a',
            'tipe_pilihan_b', 'gambar_pilihan_b',
            'tipe_pilihan_c', 'gambar_pilihan_c',
            'tipe_pilihan_d', 'gambar_pilihan_d',
            'tipe_pilihan_e', 'gambar_pilihan_e'
        ]);
    }
}
