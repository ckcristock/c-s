<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuProfile extends Model
{
    use HasFactory;
    protected $fillable = [
        'profile',
        'value_time_daytime_displacement',
        'value_time_night_displacement',
        'daytime_ordinary_hour_value',
        'night_ordinary_hour_value',
        'sunday_daytime_value',
        'sunday_night_time_value',
        'state',
        'code'
    ];
}
