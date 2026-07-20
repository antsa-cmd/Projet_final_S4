<?php

namespace App\Models;

use CodeIgniter\Model;

class Client extends Model
{
    protected $table            = 'client';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['telephone', 'nom'];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $createdField     = 'date_creation';

    public function compte()
    {
        return $this->hasOne(Compte::class, 'client_id', 'id');
    }
}
