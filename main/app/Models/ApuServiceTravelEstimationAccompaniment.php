<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuServiceTravelEstimationAccompaniment extends Model
{
    use HasFactory;
    protected $fillable = [
        'apu_service_accompaniments_id',
        'description',
        'unit',
        'amount',
        'unit_value',
        'total_value',
        'formula_amount',
        'formula_total_value',
        'travel_expense_estimation_id'
    ];

    public function travelExpenseEstimation()
    {
        return $this->belongsTo(TravelExpenseEstimation::class)->with('travelExpenseEstimationValues');
    }
}
