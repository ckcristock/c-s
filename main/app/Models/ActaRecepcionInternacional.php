<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActaRecepcionInternacional extends Model
{
    use HasFactory;
    protected $table = 'Acta_Recepcion_Internacional';
    protected $primaryKey = 'Id_Acta_Recepcion_Internacional';
    protected $fillable = [
        'Id_Bodega',
        'Id_Bodega_Nuevo',
        'Identificacion_Funcionario',
        'Id_Orden_Compra_Internacional',
        'Id_Proveedor',
        'Codigo',
        'Codigo_Qr',
        'Fecha_Creacion',
        'Observaciones',
        'Flete_Internacional',
        'Seguro_Internacional',
        'Flete_Nacional',
        'Licencia_Importacion',
        'Estado',
        'Bloquear_Parcial',
        'Tercero_Flete_Internacional',
        'Tercero_Seguro_Internacional',
        'Tercero_Flete_Nacional',
        'Tercero_Licencia_Importacion'
    ];
}
