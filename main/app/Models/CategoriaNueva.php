<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaNueva extends Model
{
    use HasFactory;
    protected $table = 'Categoria_Cueva';
    protected $primaryKey = 'Id_Categoria_Nueva';
    protected $fillable = [
        'Nombre',
        'Compra_Internacional',
        'Aplica_Separacion_Categorias',
        'Activo',
        'Fijo'
    ];
}
