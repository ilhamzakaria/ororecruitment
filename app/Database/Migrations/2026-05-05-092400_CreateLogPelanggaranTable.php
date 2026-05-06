<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogPelanggaranTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_log' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_pegawai' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'nama_pegawai' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'kode_pegawai' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'jenis_pelanggaran' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'jumlah_pelanggaran' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'status_sesi' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'tanggal_pelanggaran' => [
                'type' => 'DATETIME',
            ],
            'dibaca_oleh_hrd' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'dibaca_oleh_manager' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
        ]);
        $this->forge->addKey('id_log', true);
        $this->forge->createTable('log_pelanggaran');
    }

    public function down()
    {
        $this->forge->dropTable('log_pelanggaran');
    }
}
