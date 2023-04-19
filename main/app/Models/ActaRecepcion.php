<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActaRecepcion extends Model
{
    use HasFactory;
    protected $table = 'Acta_Recepcion';
    protected $primaryKey = 'Id_Acta_Recepcion';
    protected $fillable = [
        'Id_Bodega',
        'Id_Bodega_Nuevo',
        'Id_Punto_Dispensacion',
        'Identificacion_Funcionario',
        'Factura',
        'Fecha_Factura',
        'Observaciones',
        'Codigo',
        'Fecha_Creacion',
        'Codigo_Qr_Real',
        'Id_Proveedor',
        'Tipo',
        'Tipo_Acta',
        'Id_Orden_Compra_Nacional',
        'Id_Orden_Compra_Internacional',
        'Estado',
        'Id_Causal_Anulacion',
        'Observaciones_Anulacion',
        'Funcionario_Anula',
        'Fecha_Anulacion',
        'Codigo_Qr'
    ];
}
