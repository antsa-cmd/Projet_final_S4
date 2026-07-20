<?php

namespace App\Models;

use CodeIgniter\Model;

class Prefixe extends Model
{
    protected $table            = 'prefixe';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['prefixe', 'operateur_id'];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    public function operateur()
    {
        return $this->belongsTo(Operateur::class, 'operateur_id', 'id');
    }
}
