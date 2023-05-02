<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocInventarioAuditable extends Model
{
    use HasFactory;
    protected $table = 'Doc_Inventario_Auditable';
    protected $primaryKey = 'Id_Doc_Inventario_Auditable';
    protected $fillable = [
        'Id_Bodega',
        'Fecha_Inicio',
        'Fecha_Fin',
        'Funcionario_Digita',
        'Funcionario_Cuenta',
        'Funcionario_Autorizo',
        'Productos_Correctos',
        'Productos_Diferencia',
        'Observaciones',
        'Estado',
        'Id_Inventario_Auditable_Nuevo',
        'Lista_Productos',
        'Funcionario_Anula',
        'Fecha_Anulacion',
        'Observaciones_Anulacion',
        'Fecha',
    ];
}
