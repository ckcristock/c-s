<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuPartCommercialMaterial extends Model
{
    use HasFactory;
    protected $fillable = [
        "material_id",
        "apu_part_id",
        "unit_id",
        "q_unit",
        "q_total",
        "unit_cost",
        "total"
    ];

    public function apupart()
	{
		return $this->belongsTo(ApuPart::class);
	}
}


