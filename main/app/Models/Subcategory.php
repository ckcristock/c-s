<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $table = 'Subcategoria';
    protected $primaryKey = 'Id_Subcategoria';

    protected $fillable = ['Nombre', 'Id_Categoria_Nueva', 'Separable','Fijo'];

    /* public function category()
    {
        return $this->hasMany(Category::class, "Id_Subcategoria");
    } */

    public function subcategoryVariables()
    {
        return $this->hasMany(SubcategoryVariable::class, "subcategory_id");
    }

    public function scopeAlias($q, $alias){
        return $q->from($q->getQuery()->from." as ".$alias);
    }

    public function scopeActive($query){
        $query->where('Activo', 1);
    }

    /* public function categories(){
        return $this->belongsToMany(NewCategory::class,"categoria_nueva_subcategoria","Id_Subcategoria","Id_Categoria_Nueva");
    } */

}
