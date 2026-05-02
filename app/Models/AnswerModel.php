<?php

namespace App\Models;

use CodeIgniter\Model;

class AnswerModel extends Model
{
    protected $table            = 'jawaban_pegawai';
    protected $primaryKey       = 'id_jawaban';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pertanyaan',
        'id_pegawai',
        'nama_pegawai',
        'jawaban_dipilih',
        'jawaban_benar',
        'status_jawaban',
        'nilai',
        'tanggal_menjawab',
    ];

    protected $useTimestamps = false;
}
