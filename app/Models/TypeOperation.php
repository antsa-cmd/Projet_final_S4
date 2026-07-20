<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeOperation extends Model
{
    protected $table            = 'type_operation';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nom'];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
}
