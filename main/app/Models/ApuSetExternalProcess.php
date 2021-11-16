<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuSetExternalProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        "apu_set_id",
        "description",
        "unit",
        "amount",
        "unit_cost",
        "total"
    ];

    public function apuset()
	{
		return $this->belongsTo(ApuSet::class);
	}
}
