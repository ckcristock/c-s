<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoFuncionario extends Model
{
    use HasFactory;
    protected $table = 'Contrato_Funcionario';
    protected $primaryKey = 'Id_Contrato_Funcionario';
    protected $fillable = [
        'Identificacion_Funcionario',
        'Id_Tipo_Contrato',
        'Id_Salario',
        'Fecha_Inicio_Contrato',
        'Fecha_Fin_Contrato',
        'Id_Riesgo',
        'Valor',
        'Estado',
        'Id_Municipio',
        'Auxilio_No_Prestacional',
        'Aporte_Pension',
        'Numero_Otrosi',
        'Fecha_Preliquidado'
    ];
}
