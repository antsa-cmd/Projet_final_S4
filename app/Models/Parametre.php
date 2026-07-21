<?php

namespace App\Models;

use CodeIgniter\Model;

class Parametre extends Model
{
    protected $table            = 'parametre';
    protected $primaryKey       = 'cle';
    protected $allowedFields    = ['cle', 'valeur'];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
}
