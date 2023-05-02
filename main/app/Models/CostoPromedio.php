<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostoPromedio extends Model
{
    use HasFactory;
    protected $table = 'Costo_Promedio';
    protected $primaryKey = 'Id_Costo_Promedio';
    protected $fillable = [
        'Id_Producto',
        'Costo_Promedio',
        'Ultima_Actualizacion',
        'Costo_Anterior',
    ];
}
