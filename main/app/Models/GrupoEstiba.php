<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoEstiba extends Model
{
    use HasFactory;

    protected $table = 'Grupo_Estiba';

    protected $fillable = ['Id_Punto_Dispensacion','Nombre','Id_Bodega_Nuevo','Fecha_Vencimiento','Presentacion'];

    public function grupoEstibas(){
        return $this->hasMany(GrupoEstiba::class, 'Id_Grupo_Estiba', 'Id_Grupo_Estiba');
    }
}
