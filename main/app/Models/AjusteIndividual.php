<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjusteIndividual extends Model
{
    use HasFactory;
    protected $table = 'Ajuste_Individual';
    protected $primaryKey = 'Id_Ajuste_Individual';
    protected $fillable = [
        'Codigo',
        'Fecha',
        'Identificacion_Funcionario',
        'Tipo',
        'Id_Clase_Ajuste_Individual',
        'Origen_Destino',
        'Id_Origen_Estiba',
        'Id_Origen_Destino',
        'Codigo_Qr',
        'Estado',
        'Observacion_Anulacion',
        'Funcionario_Anula',
        'Fecha_Anulacion',
        'Estado_Salida_Bodega',
        'Estado_Entrada_Bodega',
        'Funcionario_Autoriza_Salida',
        'Fecha_Aprobacion_Salida',
        'Cambio_Estiba',
        'Id_Salida'
    ];
}
