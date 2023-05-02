<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListaGanancia extends Model
{
    use HasFactory;
    protected $table = 'Lista_Ganancia';
    protected $primaryKey = 'Id_Lista_Ganancia';
    protected $fillable = [
        'Codigo',
        'Nombre',
        'Porcentaje',
    ];
}
