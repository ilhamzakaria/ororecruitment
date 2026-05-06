<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDurasiTotalToPeserta extends Migration
{
    public function up()
    {
        $this->forge->addColumn('peserta', [
            'durasi_total_detik' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'ended_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('peserta', 'durasi_total_detik');
    }
}
