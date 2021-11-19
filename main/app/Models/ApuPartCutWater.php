<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuPartCutWater extends Model
{
    use HasFactory;

    protected $table = 'apu_part_cut_water';


    protected $fillable = [
        "material_id",
        "apu_part_id",
        "thickness_id",
        "amount",
        "long",
        "width",
        "total_length",
        "amount_cut",
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

    public function material()
	{
		return $this->belongsTo(Material::class);
	}
}


