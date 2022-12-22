<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuSetInternalProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        "apu_set_id",
        "description",
        "unit_id",
        "amount",
        "unit_cost",
        "total"
    ];

    public function apuset()
	{
		return $this->belongsTo(ApuSet::class);
	}

    public function internal()
	{
		return $this->belongsTo(InternalProcess::class, 'description');
	}

    public function unit()
	{
		return $this->belongsTo(Unit::class);
	}

}
