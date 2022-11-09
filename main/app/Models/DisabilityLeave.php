<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisabilityLeave extends Model
{
    use HasFactory;

    /***
     *El campo 'sum' ayuda a saber si el tipo de NOVEDAD
     *suma o no
     */
    protected $fillable = [
        'concept',
        'accounting_account',
        'sum',
        'percentage',
        'state',
        'novelty',
        'modality'
    ];
}
