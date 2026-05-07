<?php

namespace App\Models;

use CodeIgniter\Model;

class HrdModel extends Model
{
    protected $table            = 'hrd';
    protected $primaryKey       = 'id_hrd';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_hrd',
        'nama',
    ];
}
