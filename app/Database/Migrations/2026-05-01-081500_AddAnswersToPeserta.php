<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAnswersToPeserta extends Migration
{
    public function up()
    {
        $this->forge->addColumn('peserta', [
            'answers' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('peserta', 'answers');
    }
}
