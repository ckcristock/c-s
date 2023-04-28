<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bodegas extends Model
{
    use HasFactory;

    protected $table = 'Bodega_Nuevo';

    protected $primaryKey = 'Id_Bodega_Nuevo';

    protected $fillable = [
        'Nombre',
        'Nombre_Contrato',
        'Direccion',
        'Telefono',
        'Mapa',
        'Compra_Internacional',
        'Estado',
        'Tipo'
    ];

    public function grupo_estibas()
    {
        return $this->hasMany(GrupoEstiba::class, 'Id_Bodega_Nuevo', 'Id_Bodega_Nuevo')->with('estibas');
    }
}
