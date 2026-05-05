<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCurrentSessionToPeserta extends Migration
{
    public function up()
    {
        $this->forge->addColumn('peserta', [
            'current_session' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
                'after' => 'status'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('peserta', 'current_session');
    }
}
