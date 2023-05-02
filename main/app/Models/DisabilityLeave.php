<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisabilityLeave extends Model
{
    use HasFactory;

    //protected $with = ['cuentaContable:Id_Plan_Cuentas,Codigo_Niif,Nombre_Niif'];

    /***
     *El campo 'sum' ayuda a saber si el tipo de NOVEDAD
     *suma o no
     */
    protected $fillable = [
        'concept',
        'accounting_account',
        'sum',
        'state',
        'novelty',
        'modality'
    ];

    public function cuentaContable()
    {
        return $this->belongsTo(AccountPlan::class, 'accounting_account', 'Codigo_Niif');
    }
}
