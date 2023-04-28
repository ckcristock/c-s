<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdicionActivoFijo extends Model
{
    use HasFactory;
    protected $table = 'Adicion_Activo_Fijo';
    protected $primaryKey = 'Id_Adicion_Activo_Fijo';
    protected $fillable = [
        'Id_Activo_Fijo',
        'Id_Empresa',
        'Fecha',
        'Tipo',
        'Id_Tipo_Activo_Fijo',
        'Costo_NIIF',
        'Costo_PCGA',
        'Nit',
        'Iva',
        'Base',
        'Id_Centro_Costo',
        'Nombre',
        'Cantidad',
        'Documento',
        'Referencia',
        'Codigo',
        'Concepto',
        'Tipo_Depreciacion',
        'Costo_Rete_Fuente',
        'Costo_Rete_Ica',
        'Id_Cuenta_Rete_Ica',
        'Id_Cuenta_Rete_Fuente',
        'Identificacion_Funcionario'
    ];
}
