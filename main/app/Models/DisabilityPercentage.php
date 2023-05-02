<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisabilityPercentage extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'value',
    ];
}
