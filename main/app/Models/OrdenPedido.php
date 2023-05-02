<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenPedido extends Model
{
    use HasFactory;
    protected $table = 'Orden_Pedido';
    protected $primaryKey = 'Id_Orden_Pedido';
    protected $fillable = [
        'Id_Agentes_Cliente',
        'Orden_Compra_Cliente',
        'Archivo_Compra_Cliente',
        'Fecha_Probable_Entrega',
        'Identificacion_Funcionario',
        'Observaciones',
        'Fecha',
        'Id_Cliente',
        'Estado'
    ];
}
