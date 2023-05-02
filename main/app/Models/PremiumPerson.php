<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PremiumPerson extends Model
{
    use HasFactory;

    protected $fillable = [
        'premium_id',
        'person_id',
        'digit_person',
        'details',
        'worked_days',
        'salary',
        'total_premium'
    ];
}
