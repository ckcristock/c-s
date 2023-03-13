<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    use HasFactory;
    protected $table = "Tipo_Documento";
    protected $primaryKey = "Id_Tipo_Documento";
    protected $fillable = [
        'Codigo',
        'Cod_Dian',
        'Nombre'
    ];
}
