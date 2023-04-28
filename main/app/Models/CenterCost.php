<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CenterCost extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'parent_center_id',
        'center_type_id',
        'center_type_value',
        'status',
        'movement'
    ];
}
