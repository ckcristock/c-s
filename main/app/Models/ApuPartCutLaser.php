<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuPartCutLaser extends Model
{
    use HasFactory;

    protected $table = 'apu_part_cut_laser';


    protected $fillable = [
        "cut_laser_material_id",
        "cut_laser_material_value_id",
        "apu_part_id",
        "thickness",
        "amount",
        "long",
        "width",
        "total_length",
        "amount_cut",
        "sheets_amount",
        "diameter",
        "total_hole_perimeter",
        "time",
        "minute_value",
        "value"
    ];

    protected $hidden = [
        "updated_at","created_at",
    ];

    public function apupart()
	{
		return $this->belongsTo(ApuPart::class);
	}

    public function cutLaserMaterial()
	{
		return $this->belongsTo(CutLaserMaterial::class)->with('cutLaserMaterialValue', 'product');
	}

    public function cutLaserMaterialValue()
    {
        return $this->belongsTo(CutLaserMaterialValue::class);
    }
}
