<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estiba extends Model
{
    use HasFactory;

    protected $table = 'Estiba';

    protected $fillable = ['Nombre','Id_Grupo_Estiba','Id_Bodega_Nuevo','Id_Punto_Dispensacion','Codigo_Barras','Estado'];

    public function grupoEstibas(){
        return $this->hasMany(GrupoEstiba::class, 'Id_Grupo_Estiba', 'Id_Grupo_Estiba');
    }
    public function bodegas(){
        return $this->hasMany(Bodegas::class, 'Id_Bodega_Nuevo', 'Id_Bodega_Nuevo')->with('estibas');
    }
}
