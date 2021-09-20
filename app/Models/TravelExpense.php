<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelExpense extends Model
{
    use HasFactory;
    protected $fillable = [
        'person_id', 'origen', 'destiny', 'travel_type', 'departure_date', 'arrival_date', 'days_number', 'total', 'observation'
    ];
}
