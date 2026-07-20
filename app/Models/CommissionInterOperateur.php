<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionInterOperateur extends Model
{
    protected $table            = 'commission_inter_operateur';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'operateur_source_id',
        'operateur_destination_id',
        'montant_min',
        'montant_max',
        'pourcentage',
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
}
