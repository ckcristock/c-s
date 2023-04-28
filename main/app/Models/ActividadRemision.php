<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadRemision extends Model
{
    use HasFactory;
    protected $table = 'Actividad_Remision';
    protected $primaryKey = 'Id_Actividad_Remision';
    protected $fillable = [
        'Id_Remision',
        'Identificacion_Funcionario',
        'Fecha',
        'Detalles',
        'Estado',
    ];
}
