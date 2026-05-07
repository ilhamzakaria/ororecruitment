<?php

namespace App\Models;

use CodeIgniter\Model;

class SessionStatusModel extends Model
{
    protected $table            = 'status_sesi_peserta';
    protected $primaryKey       = 'id_status';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_pegawai',
        'nomor_sesi',
        'durasi_menit',
        'waktu_mulai',
        'waktu_sisa',
        'status_sesi',
        'tanggal_selesai',
    ];

    public function getStatus(string $idPegawai, int $sessionNumber)
    {
        return $this->where('id_pegawai', $idPegawai)
                    ->where('nomor_sesi', $sessionNumber)
                    ->first();
    }
}
