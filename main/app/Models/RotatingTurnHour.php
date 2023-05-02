<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RotatingTurnHour extends Model
{
    use HasFactory;
    protected $fillable = [
        'person_id',
        'rotating_turn_id',
        'date',
        'weeks_number',
    ];
    public function turnoRotativo()
    {
        return $this->belongsTo(RotatingTurn::class, 'rotating_turn_id');
    }
}
