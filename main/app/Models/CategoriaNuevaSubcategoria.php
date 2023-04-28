<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaNuevaSubcategoria extends Model
{
    use HasFactory;
    protected $table = 'Categoria_Nueva_Subcategoria';
    protected $primaryKey = 'id';
    protected $fillable = [
        'Id_Categoria_Nueva',
        'Id_Subcategoria'
    ];
}
