<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryRotatingTurnHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'rotating_turn_hour_id',
        'person_id',
        'batch',
        'action'
    ];

    public function rotating_turn_hour() {
        return $this->belongsTo(RotatingTurnHour::class)->with('person', 'turnoRotativo');
    }

    public function person() {
        return $this->belongsTo(Person::class)->fullName();
    }

}
