<?php

namespace App\Models;

use CodeIgniter\Model;

class SessionQuestionModel extends Model
{
    protected $primaryKey       = 'id_pertanyaan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'urutan_pertanyaan',
        'tipe_pertanyaan',
        'isi_pertanyaan',
        'gambar_pertanyaan',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'pilihan_e',
        'gambar_pilihan_a',
        'gambar_pilihan_b',
        'gambar_pilihan_c',
        'gambar_pilihan_d',
        'gambar_pilihan_e',
        'tipe_pilihan_a',
        'tipe_pilihan_b',
        'tipe_pilihan_c',
        'tipe_pilihan_d',
        'tipe_pilihan_e',
        'jawaban_benar',
        'status_pertanyaan',
        'tanggal_dibuat',
        'tanggal_diubah',
    ];

    public function setSession(int $session)
    {
        $this->table = "pertanyaan_sesi_$session";
        return $this;
    }
}
