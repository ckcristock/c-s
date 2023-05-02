<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutLaserMaterialValue extends Model
{
    use HasFactory;
    protected $fillable = [
        'cut_laser_material_id',
        'thickness',
        'actual_speed',
        'seconds_percing',
    ];
    protected $hidden = ['unit_value'];

    public function cutLaserMaterial()
    {
        return $this->belongsTo(CutLaserMaterial::class);
    }

}
