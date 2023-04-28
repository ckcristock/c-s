<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadContabilidad extends Model
{
    use HasFactory;
    protected $table = 'Actividad_Contabilidad';
    protected $primaryKey = 'Id_Actividad_Contabilidad';
    protected $fillable = [
        'Id_Registro',
        'Identificacion_Funcionario',
        'Fecha',
        'Detalles',
        'Estado',
        'Modulo',
    ];
}
