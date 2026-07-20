<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFrais extends Model
{
    protected $table            = 'bareme_frais';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['type_operation_id', 'montant_min', 'montant_max', 'frais'];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
}
