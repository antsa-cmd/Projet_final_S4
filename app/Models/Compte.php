<?php

namespace App\Models;

use CodeIgniter\Model;

class Compte extends Model
{
    protected $table            = 'compte';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['client_id', 'solde'];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
}
