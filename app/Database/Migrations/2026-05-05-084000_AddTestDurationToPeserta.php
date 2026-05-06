<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTestDurationToPeserta extends Migration
{
    public function up()
    {
        $this->forge->addColumn('peserta', [
            'test_duration' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 30, // in minutes
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('peserta', 'test_duration');
    }
}
