<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuPartMachineTool extends Model
{
    use HasFactory;

    protected $fillable = [
        "description",
        "unit_id",
        "apu_part_id",
        "q_unit" ,
        "q_total" ,
        "unit_cost" ,
        "total"
    ];

    protected $hidden = [
        "updated_at","created_at",
     ];


    public function apupart()
	{
		return $this->belongsTo(ApuPart::class);
	}

    public function unit()
	{
		return $this->belongsTo(Unit::class);
	}
}



