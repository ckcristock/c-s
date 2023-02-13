<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        "name",
        "product_id",
        "unit",
        "unit_price",
        "cut_water",
        "cut_laser",
        "type",
        "kg_value",
        "value_aux"
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
        return $this->hasOne(Product::class, 'Id_Producto', "product_id");
    }
}
