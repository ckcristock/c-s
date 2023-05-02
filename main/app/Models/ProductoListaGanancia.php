<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoListaGanancia extends Model
{
    use HasFactory;

    protected $table = 'Producto_Lista_Ganancia';
    protected $primaryKey = 'Id_Producto_Lista_Ganancia';
    protected $fillable = [
        'Cum',
        'Precio',
        'Id_Lista_Ganancia',
        'Precio_Anterior',
        'Ultima_Actualizacion',
        'Estado',
    ];
}
