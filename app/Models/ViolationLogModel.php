<?php

namespace App\Models;

use CodeIgniter\Model;

class ViolationLogModel extends Model
{
    protected $table            = 'log_pelanggaran';
    protected $primaryKey       = 'id_log';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_pegawai',
        'nama_pegawai',
        'jenis_pelanggaran',
        'keterangan',
        'waktu_pelanggaran',
    ];

    public function countByPegawai(string $idPegawai)
    {
        return $this->where('id_pegawai', $idPegawai)->countAllResults();
    }
}
