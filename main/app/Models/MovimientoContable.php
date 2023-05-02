<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoContable extends Model
{
    use HasFactory;
    protected $table = 'Movimiento_Contable';
    protected $primaryKey = 'Id_Movimiento_Contable';
    protected $fillable = [
        'Id_Plan_Cuenta',
        'Fecha_Movimiento',
        'Id_Modulo',
        'Id_Registro_Modulo',
        'Debe',
        'Haber',
        'Debe_Niif',
        'Haber_Niif',
        'Nit',
        'Tipo_Nit',
        'Estado',
        'Documento',
        'Detalles',
        'Fecha_Registro',
        'Id_Centro_Costo',
        'Mantis',
        'Numero_Comprobante'
    ];
}
