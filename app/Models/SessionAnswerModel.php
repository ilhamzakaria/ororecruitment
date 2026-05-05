<?php

namespace App\Models;

use CodeIgniter\Model;

class SessionAnswerModel extends Model
{
    protected $primaryKey       = 'id_jawaban';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_pegawai',
        'nama_pegawai',
        'id_pertanyaan',
        'nomor_pertanyaan',
        'jawaban_pegawai',
        'jawaban_benar',
        'status_jawaban',
        'nilai',
        'tanggal_menjawab',
    ];

    public function setSession(int $session)
    {
        $this->table = "jawaban_sesi_$session";
        return $this;
    }
}
