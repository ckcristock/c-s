<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoCronogramaRemision extends Model
{
    use HasFactory;
    protected $table = 'Punto_Cronograma_Remision';
    protected $primaryKey = 'Id_Punto_Cronograma_Remision';
    protected $fillable = [
        'Id_Punto',
        'Id_Cronograma'
    ];
}
