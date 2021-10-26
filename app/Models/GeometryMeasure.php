<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeometryMeasure extends Model
{
    protected $table = 'geometries_measures';

    protected $fillable = [
        "geometry_id",
        "measure_id",
    ];
}
