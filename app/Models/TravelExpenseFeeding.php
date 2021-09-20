<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelExpenseFeeding extends Model
{
    use HasFactory;
    protected $fillable = [
        'stay', 'type', 'breakfast', 'rate', 'total', 'travel_expense_id', 'person_type'
    ];
}
