<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $table = 'subcategoria';
    protected $primaryKey = 'Id_Subcategoria';

    protected $fillable = ['Nombre', 'Separable'];

    public function category()
    {
        return $this->hasMany(Category::class, "Id_Subcategoria");
    }

    public function subcategoryVariables()
    {
        return $this->hasMany(SubcategoryVariable::class, "subcategory_id");
    }

    public function subcategories(){
        return $this->belongsToMany(NewCategory::class,"categoria_nueva_subcategoria","Id_Subcategoria","Id_Categoria_Nueva");
    }

}
