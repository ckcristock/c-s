<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RotatingTurnHour extends Model
{
    use HasFactory;
    protected $fillable= [
        'person_id',
        'date',
        'weeks_number',
        'rotating_turn_id'
    ];
}

