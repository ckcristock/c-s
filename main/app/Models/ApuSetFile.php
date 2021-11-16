<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuSetFile extends Model
{
    use HasFactory;

    protected $fillable = [
        "file",
        "apu_set_id"
    ];

    protected $hidden = [
        "updated_at","created_at",
    ];

    public function apuset()
	{
		return $this->belongsTo(ApuSet::class);
	}
}
