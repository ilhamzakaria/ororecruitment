<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOrderToPertanyaanTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pertanyaan_tes', [
            'urutan_pertanyaan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_pertanyaan'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pertanyaan_tes', 'urutan_pertanyaan');
    }
}
