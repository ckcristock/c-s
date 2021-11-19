<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutLaserMaterialValue extends Model
{
    use HasFactory;
    protected $fillable = ['thickness', 'unit_value', 'actual_speed', 'seconds_percing', 'cut_laser_material_id'];
}
