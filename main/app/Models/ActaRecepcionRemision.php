<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActaRecepcionRemision extends Model
{
    use HasFactory;
    protected $table = 'Acta_Recepcion_Remision';
    protected $primaryKey = 'Id_Acta_Recepcion_Remision';
    protected $fillable = [
        'Codigo',
        'Id_Punto_Dispensacion',
        'Id_Bodega',
        'Id_Bodega_Nuevo',
        'Identificacion_Funcionario',
        'Observaciones',
        'Id_Remision',
        'Tipo',
        'Fecha',
        'Codigo_Qr',
        'Estado'
    ];
}
