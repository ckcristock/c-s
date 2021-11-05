<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuPartFile extends Model
{
    use HasFactory;

    protected $fillable = [
        "url",
        "apu_part_id"
    ];

    protected $hidden = [
        "updated_at","created_at",
    ];

    public function apupart()
	{
		return $this->belongsTo(ApuPart::class);
	}
}
