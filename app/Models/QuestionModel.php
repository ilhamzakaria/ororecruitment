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
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'jawaban_benar',
        'status_pertanyaan',
        'dibuat_oleh',
        'role_pembuat',
        'tanggal_dibuat',
        'tanggal_diubah',
    ];

    protected $useTimestamps = false; // We use manual columns from requirement
}
