<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelExpenseHotel extends Model
{
    use HasFactory;
    protected $fillable = [
        'hotel_id', 'n_nights', 'who_cancels', 'travel_expense_id'
    ];
}
