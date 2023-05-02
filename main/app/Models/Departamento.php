<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;
    protected $table = 'Departamento';
    protected $primaryKey = 'Id_Departamento';
    protected $fillable = [
        'Nombre',
        'Codigo'
    ];
}
