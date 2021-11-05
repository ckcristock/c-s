<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Measure extends Model
{
    protected $fillable = [
		"name",
		"measure",
	];

    protected $hidden = [
        "updated_at","created_at",
    ];


    public function GeometriesM()
	{
		return $this->belongsToMany(Geometry::class);
	}

    public function ApuPartRawMaterial()
	{
		return $this->belongsToMany(ApuPartRawMaterial::class);
	}

}
