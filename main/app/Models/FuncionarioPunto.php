<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuncionarioPunto extends Model
{
    use HasFactory;
    protected $table = 'Funcionario_Punto';
    protected $primaryKey = 'Id_Funcionario_Punto';
    protected $fillable = [
        'Id_Punto_Dispensacion',
        'Identificacion_Funcionario',
        'Fecha',
    ];
}
