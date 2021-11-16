<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialField extends Model
{
    use HasFactory;
    protected $fillable = [
        'material_id',
        'type',
        'property',
        'value'
    ];
}
