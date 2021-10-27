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

    public function GeometriesM()
	{
		return $this->belongsToMany(Geometry::class);
	}

}