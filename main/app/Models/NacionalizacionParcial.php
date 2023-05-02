<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NacionalizacionParcial extends Model
{
    use HasFactory;
    protected $table = 'Nacionalizacion_Parcial';
    protected $primaryKey = 'Id_Nacionalizacion_Parcial';
    protected $fillable = [
        'Id_Acta_Recepcion_Internacional',
        'Identificacion_Funcionario',
        'Codigo',
        'Fecha_Registro',
        'Tasa_Cambio',
        'Estado',
        'Observaciones',
        'Tramite_Sia',
        'Formulario',
        'Cargue',
        'Gasto_Bancario',
        'Tercero_Tramite_Sia',
        'Tercero_Formulario',
        'Tercero_Cargue',
        'Tercero_Gasto_Bancario',
        'Descuento_Parcial',
    ];
}
