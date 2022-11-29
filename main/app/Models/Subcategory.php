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

}
