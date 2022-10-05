<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bodegas extends Model
{
    use HasFactory;

    protected $table ='Bodega_Nuevo';

    protected $fillable = ['Nombre','Nombre_Contrato','Direccion','Telefono','Mapa','Compra_Internacional','Estado','Tipo'];

    public function estibas(){
        return $this->hasMany(Estiba::class, 'Id_Bodega_Nuevo', 'Id_Bodega_Nuevo')->with('grupoEstibas');
    }
}
