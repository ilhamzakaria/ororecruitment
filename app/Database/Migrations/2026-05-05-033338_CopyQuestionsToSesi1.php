<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CopyQuestionsToSesi1 extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Check if pertanyaan_tes table exists before copying
        if ($db->tableExists('pertanyaan_tes')) {
            $sql = "INSERT INTO pertanyaan_sesi_1 (
                        isi_pertanyaan,
                        tipe_pertanyaan,
                        gambar_pertanyaan,
                        pilihan_a,
                        pilihan_b,
                        pilihan_c,
                        pilihan_d,
                        pilihan_e,
                        gambar_pilihan_a,
                        gambar_pilihan_b,
                        gambar_pilihan_c,
                        gambar_pilihan_d,
                        gambar_pilihan_e,
                        jawaban_benar,
                        status_pertanyaan,
                        urutan_pertanyaan,
                        tanggal_dibuat,
                        tanggal_diubah
                    )
                    SELECT
                        isi_pertanyaan,
                        tipe_pertanyaan,
                        gambar_pertanyaan,
                        pilihan_a,
                        pilihan_b,
                        pilihan_c,
                        pilihan_d,
                        pilihan_e,
                        gambar_pilihan_a,
                        gambar_pilihan_b,
                        gambar_pilihan_c,
                        gambar_pilihan_d,
                        gambar_pilihan_e,
                        jawaban_benar,
                        status_pertanyaan,
                        urutan_pertanyaan,
                        tanggal_dibuat,
                        tanggal_diubah
                    FROM pertanyaan_tes";
            
            $db->query($sql);
        }
    }

    public function down()
    {
        // No need to delete from pertanyaan_sesi_1 on down as it's just a copy
        // But if needed: $db->table('pertanyaan_sesi_1')->truncate();
    }
}
