<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewCategory extends Model
{
    protected $table = 'Categoria_Nueva';
    protected $primaryKey = 'Id_Categoria_Nueva';
    protected $fillable = ['Id_Categoria_Nueva', 'Nombre', 'Compra_Internacional', 'Aplica_Separacion_Categorias', 'Fijo'];

    /* public function subcategories(){
        return $this->belongsToMany(Subcategory::class,"categoria_nueva_subcategoria","Id_Categoria_Nueva","Id_Subcategoria");
    }
 */

    public function categoryVariables()
    {
        return $this->hasMany(CategoryVariable::class, "category_id");
    }

    public function subcategory()
    {
        return $this->hasMany(Subcategory::class, "Id_Categoria_Nueva");
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'Id_Categoria_Nueva', 'Id_Categoria_Nueva');
    }

    public function scopeActive($query)
    {
        $query->where('Activo', 1);
    }
}
