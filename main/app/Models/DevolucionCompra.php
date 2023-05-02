<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevolucionCompra extends Model
{
    use HasFactory;

    protected $table = 'Devolucion_Compra';
    protected $primaryKey = 'Id_Devolucion_Compra';
    protected $fillable = [
        'Id_No_Conforme',
        'Identificacion_Funcionario',
        'Observaciones',
        'Fecha',
        'Codigo',
        'Codigo_Qr',
        'Id_Proveedor',
        'Id_Bodega',
        'Id_Bodega_Nuevo',
        'Estado',
        'Estado_Alistamiento',
        'Fase_1',
        'Inicio_Fase1',
        'Fin_Fase1',
        'Fase_2',
        'Inicio_Fase2',
        'Guia',
        'Empresa_Envio'
    ];
}
