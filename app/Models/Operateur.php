<?php

namespace App\Models;

use CodeIgniter\Model;

class Operateur extends Model
{
    protected $table            = 'operateur';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nom'];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
}
