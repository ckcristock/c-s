<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuPartRawMaterialMeasure extends Model
{
    use HasFactory;

    protected $fillable = [
        "measure_id",
        "value",
        "apu_part_raw_material_id"
    ];

}



