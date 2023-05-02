<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocInventarioFisico extends Model
{
    use HasFactory;
    protected $table = 'Doc_Inventario_Fisico';
    protected $primaryKey = 'Id_Doc_Inventario_Fisico';
    protected $fillable = [
        'Id_Estiba',
        'Fecha_Inicio',
        'Fecha_Fin',
        'Funcionario_Digita',
        'Funcionario_Cuenta',
        'Funcionario_Autorizo',
        'Productos_Correctos',
        'Productos_Diferencia',
        'Observaciones',
        'Estado',
        'Id_Inventario_Fisico_Nuevo',
        'Lista_Productos',
        'Funcionario_Anula',
        'Fecha_Anulacion',
        'Observaciones_Anulacion'
    ];
}
