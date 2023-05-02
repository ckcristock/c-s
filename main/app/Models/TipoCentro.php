<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCentro extends Model
{
    use HasFactory;
    protected $table = 'Tipo_Centro';
    protected $primaryKey = 'Id_Tipo_Centro';
    protected $fillable = [
        'Nombre'
    ];
}
