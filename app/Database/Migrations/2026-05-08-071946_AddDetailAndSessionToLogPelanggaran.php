<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDetailAndSessionToLogPelanggaran extends Migration
{
    public function up()
    {
        $this->forge->addColumn('log_pelanggaran', [
            'detail_pelanggaran' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'jenis_pelanggaran'
            ],
            'nomor_sesi' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
                'after' => 'jumlah_pelanggaran'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('log_pelanggaran', ['detail_pelanggaran', 'nomor_sesi']);
    }
}
