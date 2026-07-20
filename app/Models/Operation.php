<?php

namespace App\Models;

use CodeIgniter\Model;

class Operation extends Model
{
    protected $table            = 'operation';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'type_operation_id',
        'compte_source',
        'compte_destination',
        'montant',
        'frais',
        'commission',
        'inclure_frais_retrait',
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $createdField     = 'date_operation';

    public function typeOperation()
    {
        return $this->belongsTo(TypeOperation::class, 'type_operation_id', 'id');
    }
}
