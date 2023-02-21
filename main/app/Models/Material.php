<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        "id",
        "material_id",
    ];


    public function rawmaterial()
	{
		return $this->hasOne(ApuPartRawMaterial::class);
	}

    public function commercialMaterial()
	{
		return $this->hasOne(ApuPartCommercialMaterial::class);
	}

    public function materialField()
    {
        return $this->hasMany(MaterialField::class);
    }

    public function materialThickness()
    {
        return $this->hasMany(MaterialThickness::class)->with('thickness');
    }


    public function product()
    {
        return $this->belongsTo(Product::class, 'material_id', 'Id_Producto')->select('Id_Producto', 'Unidad_Medida', 'Nombre_Comercial as name')->with('unit');
    }
}
