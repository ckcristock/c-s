<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaNueva extends Model
{
    use HasFactory;
    protected $table = 'Categoria_Nueva';
    protected $primaryKey = 'Id_Categoria_Nueva';
    protected $fillable = [
        'Nombre',
        'Compra_Internacional',
        'Aplica_Separacion_Categorias',
        'Activo',
        'Fijo',
    ];
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
