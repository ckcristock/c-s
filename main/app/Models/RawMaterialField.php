<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialField extends Model
{
    use HasFactory;
    protected $fillable = [
        'raw_material_id',
        'type',
        'property',
        'value'
    ];
}
