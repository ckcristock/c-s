<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuSetPartList extends Model
{
    use HasFactory;

    protected $fillable = [
        "apu_set_id",
        "apu_part_id",
        "apu_set_child_id",
        "apu_type",
        "unit",
        "amount",
        "unit_cost",
        "total"
    ];

    public function apuset()
	{
		return $this->belongsTo(ApuSet::class, 'apu_set_child_id');
	}

    public function apupart()
	{
		return $this->belongsTo(ApuPart::class, 'apu_part_id');
	}
    
}
