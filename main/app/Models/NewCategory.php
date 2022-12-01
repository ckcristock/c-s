<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewCategory extends Model
{
    protected $table = 'Categoria_Nueva';
    protected $primaryKey = 'Id_Categoria_Nueva';
    protected $fillable = ['Id_Categoria_Nueva','Nombre','Compra_Internacional','Aplica_Separacion_Categorias'];

    public function subcategories(){
        return $this->belongsToMany(Subcategory::class,"categoria_nueva_subcategoria","Id_Categoria_Nueva","Id_Subcategoria");
    }

    /* public function subcategory(){
        return $this->hasMany(Subcategory::class,"Id_Categoria_Nueva");
    } */
}
