<?php

namespace App\Models;

use CodeIgniter\Model;

class ManagerModel extends Model
{
    protected $table            = 'manager';
    protected $primaryKey       = 'id_manager';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_manager',
        'nama',
    ];
}
