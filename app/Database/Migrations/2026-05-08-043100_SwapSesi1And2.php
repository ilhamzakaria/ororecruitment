<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SwapSesi1And2 extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        echo "Starting swap of Sesi 1 and Sesi 2...\n";
        
        $db->transStart();

        // 1. Swap Question Tables
        // Using a temporary name to avoid collision
        $db->query("RENAME TABLE pertanyaan_sesi_1 TO pertanyaan_sesi_temp");
        $db->query("RENAME TABLE pertanyaan_sesi_2 TO pertanyaan_sesi_1");
        $db->query("RENAME TABLE pertanyaan_sesi_temp TO pertanyaan_sesi_2");
        echo "- Swapped question tables.\n";

        // 2. Swap Answer Tables
        $db->query("RENAME TABLE jawaban_sesi_1 TO jawaban_sesi_temp");
        $db->query("RENAME TABLE jawaban_sesi_2 TO jawaban_sesi_1");
        $db->query("RENAME TABLE jawaban_sesi_temp TO jawaban_sesi_2");
        echo "- Swapped answer tables.\n";

        // 3. Update Metadata in status_sesi_peserta
        // Use 0 as an intermediate value for the swap
        $db->query("UPDATE status_sesi_peserta SET nomor_sesi = 0 WHERE nomor_sesi = 1");
        $db->query("UPDATE status_sesi_peserta SET nomor_sesi = 1 WHERE nomor_sesi = 2");
        $db->query("UPDATE status_sesi_peserta SET nomor_sesi = 2 WHERE nomor_sesi = 0");
        echo "- Updated status_sesi_peserta metadata.\n";

        // 4. Update Metadata in kontrol_sesi_pegawai
        $db->query("UPDATE kontrol_sesi_pegawai SET nomor_sesi = 0 WHERE nomor_sesi = 1");
        $db->query("UPDATE kontrol_sesi_pegawai SET nomor_sesi = 1 WHERE nomor_sesi = 2");
        $db->query("UPDATE kontrol_sesi_pegawai SET nomor_sesi = 2 WHERE nomor_sesi = 0");
        echo "- Updated kontrol_sesi_pegawai metadata.\n";

        // 5. Update Metadata in log_aktivitas_tes
        $db->query("UPDATE log_aktivitas_tes SET nomor_sesi = 0 WHERE nomor_sesi = 1");
        $db->query("UPDATE log_aktivitas_tes SET nomor_sesi = 1 WHERE nomor_sesi = 2");
        $db->query("UPDATE log_aktivitas_tes SET nomor_sesi = 2 WHERE nomor_sesi = 0");
        echo "- Updated log_aktivitas_tes metadata.\n";

        // 6. Update Metadata in log_pelanggaran (status_sesi column)
        $db->query("UPDATE log_pelanggaran SET status_sesi = 'Sesi Temp' WHERE status_sesi = 'Sesi 1'");
        $db->query("UPDATE log_pelanggaran SET status_sesi = 'Sesi 1' WHERE status_sesi = 'Sesi 2'");
        $db->query("UPDATE log_pelanggaran SET status_sesi = 'Sesi 2' WHERE status_sesi = 'Sesi Temp'");
        echo "- Updated log_pelanggaran metadata.\n";

        $db->transComplete();

        if ($db->transStatus() === false) {
            echo "CRITICAL: Transaction failed! Changes rolled back.\n";
        } else {
            echo "SUCCESS: Sesi 1 and Sesi 2 have been swapped successfully.\n";
        }
    }

    public function down()
    {
        // Swapping again reverts the changes
        $this->up();
    }
}
