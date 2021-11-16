<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        "name",
        "unit",
        "unit_price",
        "cut_water",
        "cut_laser",
        "type",
        "kg_value"
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
}
