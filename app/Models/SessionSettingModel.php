<?php

namespace App\Models;

use CodeIgniter\Model;

class SessionSettingModel extends Model
{
    protected $table            = 'pengaturan_sesi';
    protected $primaryKey       = 'id_pengaturan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_sesi',
        'nama_sesi',
        'durasi_menit',
    ];

    public function getBySessionNumber(int $sessionNumber)
    {
        return $this->where('id_sesi', $sessionNumber)->first();
    }
}
