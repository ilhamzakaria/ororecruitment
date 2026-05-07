<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // IDs are strings like PGW001
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id',
        'username',
        'password',
        'role',
        'status',
        'status_pengguna',
        'session_id',
        'created_at',
        'updated_at',
        'tanggal_eliminasi',
        'dieliminasi_oleh',
    ];

    public function setStatus(string $id, string $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    public function eliminate(string $id, string $by)
    {
        return $this->update($id, [
            'status_pengguna' => 'eliminasi',
            'tanggal_eliminasi' => date('Y-m-d H:i:s'),
            'dieliminasi_oleh' => $by
        ]);
    }

    public function restore(string $id)
    {
        return $this->update($id, [
            'status_pengguna' => 'aktif',
            'tanggal_eliminasi' => null,
            'dieliminasi_oleh' => null
        ]);
    }
}
