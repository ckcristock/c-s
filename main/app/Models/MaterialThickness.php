<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialThickness extends Model
{
    use HasFactory;
    protected $fillable = [
        'thickness_id',
        'material_id',
        'value'
    ];

    public function thickness()
    {
        return $this->belongsTo(Thickness::class);
    }
}
