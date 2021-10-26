<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        "name",
        "unit",
        "unit_price",
        "cut_water",
        "cut_laser",
        "type",
    ];
}
