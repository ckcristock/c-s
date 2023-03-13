<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuPartRawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        "geometry_id",
        "apu_part_id",
        "material_id",
        "weight_kg",
        "q",
        "weight_total",
        "value_kg",
        "total_value"
    ];

    protected $hidden = [
        "updated_at","created_at",
    ];

    public function apupart()
	{
		return $this->belongsTo(ApuPart::class);
	}

    public function measures()
	{
		return $this->belongsToMany(Measure::class, 'apu_part_raw_material_measures');
	}

    public function geometry()
	{
		return $this->belongsTo(Geometry::class);
	}

    public function material()
	{
         return $this->belongsTo(RawMaterialMaterial::class, 'material_id', 'id')->with('product');
	}


}



