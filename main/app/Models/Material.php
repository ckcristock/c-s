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
    ];

    protected $hidden = [
        "updated_at","created_at",
    ];


    public function rowmaterial()
	{
		return $this->hasOne(ApuPartRawMaterial::class);
	}

    public function commercialMaterial()
	{
		return $this->hasOne(ApuPartCommercialMaterial::class);
	}

    public function cutWater()
	{
		return $this->hasOne(ApuPartCutWater::class);
	}

    public function cutLaser()
	{
		return $this->hasOne(ApuPartCutLaser::class);
	}
}
