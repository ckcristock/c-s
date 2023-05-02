<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DireccionDian extends Model
{
    use HasFactory;
    protected $table = 'Direccion_Dian';
    protected $primaryKey = 'Id_Direccion_Dian';
    protected $fillable = [
        'Codigo',
        'Descripcion'
    ];

}
