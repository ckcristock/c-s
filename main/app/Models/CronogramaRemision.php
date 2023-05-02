<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CronogramaRemision extends Model
{
    use HasFactory;
    protected $table = 'Cronograma_Remision';
    protected $primaryKey = 'Id_Cronograma_Remision';
    protected $fillable = [
        'Dia',
        'Semana',
        'Fecha_Asignacion',
        'Id_Funcionario',
    ];
}
