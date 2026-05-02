<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixIdColumnTypes extends Migration
{
    public function up()
    {
        // Fix pegawai table id_user to VARCHAR(50)
        $this->db->query("ALTER TABLE pegawai MODIFY COLUMN id_user VARCHAR(50) NOT NULL");

        // Fix hrd table id_hrd to VARCHAR(50)
        $this->db->query("ALTER TABLE hrd MODIFY COLUMN id_hrd VARCHAR(50) NOT NULL");
    }

    public function down()
    {
        // Revert to INT if necessary, but this might break data
        $this->db->query("ALTER TABLE pegawai MODIFY COLUMN id_user INT(11) NOT NULL");
        $this->db->query("ALTER TABLE hrd MODIFY COLUMN id_hrd INT(11) NOT NULL");
    }
}
