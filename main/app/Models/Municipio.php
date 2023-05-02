<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;
    protected $table = 'Municipio';
    protected $primaryKey = 'Id_Municipio';
    protected $fillable = [
        'Id_Departamento',
        'Nombre',
        'Codigo',
        'Codigo_Dane',
        'municipalities_id'
    ];
}
