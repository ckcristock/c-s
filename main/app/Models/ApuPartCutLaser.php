<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuPartCutLaser extends Model
{
    use HasFactory;

    protected $table = 'apu_part_cut_laser';


    protected $fillable = [
        "material_id",
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

    public function apupart()
	{
		return $this->belongsTo(ApuPart::class);
	}
}
