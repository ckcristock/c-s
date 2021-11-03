<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuPartIndirectCost extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "apu_part_id",
        "percentage",
        "value"
    ];

    public function apupart()
	{
		return $this->belongsTo(ApuPart::class);
	}
}

