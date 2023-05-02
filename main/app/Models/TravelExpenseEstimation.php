<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelExpenseEstimation extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'unit',
        'displacement',
        'destination',
        'land_national_value',
        'land_international_value',
        'aerial_national_value',
        'aerial_international_value',
        'international_value',
        'national_value',
        'unit_value',
        'formula_amount',
        'formula_total_value'
    ];

    public function travelExpenseEstimationValues()
    {
        return $this->hasOne(TravelExpenseEstimationValues::class);
    }

}
