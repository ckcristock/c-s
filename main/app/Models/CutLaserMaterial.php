<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutLaserMaterial extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'formula'];

    public function cutLaserMaterialValue()
    {
        return $this->hasMany(CutLaserMaterialValue::class);
    }

}
