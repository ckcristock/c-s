<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodigoCiiu extends Model
{
    use HasFactory;
    protected $table = 'Codigo_Ciiu';
    protected $primaryKey = 'Id_Codigo_Ciiu';
    protected $fillable = [
        'Codigo',
        'Descripcion'
    ];
}
