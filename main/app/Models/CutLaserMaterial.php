<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutLaserMaterial extends Model
{
    use HasFactory;
    protected $fillable = ['material_id', 'formula'];

    public function cutLaserMaterialValue()
    {
        return $this->hasMany(CutLaserMaterialValue::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'material_id', 'Id_Producto')->select('Id_Producto', 'Unidad_Medida', 'Nombre_Comercial as name')->with('unit');
    }

}
