<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoDocInventarioFisico extends Model
{
    use HasFactory;

    protected $table = 'Producto_Doc_Inventario_Fisico';
    protected $primaryKey = 'Id_Producto_Doc_Inventario_Fisico';
    protected $fillable = [
        'Id_Producto',
        'Id_Inventario_Nuevo',
        'Primer_Conteo',
        'Fecha_Primer_Conteo',
        'Segundo_Conteo',
        'Fecha_Segundo_Conteo',
        'Cantidad_Auditada',
        'Funcionario_Cantidad_Auditada',
        'Cantidad_Inventario',
        'Id_Doc_Inventario_Fisico',
        'Lote',
        'Fecha_Vencimiento',
        'Actualizado',
    ];
}
