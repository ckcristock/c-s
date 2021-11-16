<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuSetIndirectCost extends Model
{
    use HasFactory;

    protected $fillable = [
        "apu_set_id",
        "name",
        "percentage",
        "value"
    ];

    public function apuset()
	{
		return $this->belongsTo(ApuSet::class);
	}
}
