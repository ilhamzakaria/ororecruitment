<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionModel extends Model
{
    protected $table            = 'pertanyaan_tes';
    protected $primaryKey       = 'id_pertanyaan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'isi_pertanyaan',
        'tipe_pertanyaan',
        'gambar_pertanyaan',
        'pilihan_a',
        'tipe_pilihan_a',
        'gambar_pilihan_a',
        'pilihan_b',
        'tipe_pilihan_b',
        'gambar_pilihan_b',
        'pilihan_c',
        'tipe_pilihan_c',
        'gambar_pilihan_c',
        'pilihan_d',
        'tipe_pilihan_d',
        'gambar_pilihan_d',
        'pilihan_e',
        'tipe_pilihan_e',
        'gambar_pilihan_e',
        'jawaban_benar',
        'status_pertanyaan',
        'dibuat_oleh',
        'role_pembuat',
        'tanggal_dibuat',
        'tanggal_diubah',
        'urutan_pertanyaan',
    ];

    protected $useTimestamps = false; // We use manual columns from requirement
}
