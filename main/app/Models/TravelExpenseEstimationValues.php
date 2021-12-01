<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelExpenseEstimationValues extends Model
{
    use HasFactory;
    protected $fillable = [
        'travel_expense_estimation_id',
        'land_national_value',
        'land_international_value',
        'aerial_national_value',
        'aerial_international_value'
    ];
}
