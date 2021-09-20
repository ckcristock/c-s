<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelExpenseTaxi extends Model
{
    use HasFactory;
    protected $fillable = [
        'taxi_id', 'journeys', 'travel_expense_id'
    ];
}
