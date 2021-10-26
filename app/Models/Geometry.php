<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Geometry extends Model
{
    protected $fillable = [
		"name",
		"image",
		"weight_formula",
	];

    public function measures()
	{
		return $this->belongsToMany(Measure::class, 'geometries_measures');
	}

}
