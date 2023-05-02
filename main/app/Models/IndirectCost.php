<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndirectCost extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'percentage',
        'apply_service',
        'state'
    ];
}
