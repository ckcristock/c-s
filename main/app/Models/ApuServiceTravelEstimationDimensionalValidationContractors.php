<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuServiceTravelEstimationDimensionalValidationContractors extends Model
{
    use HasFactory;
    protected $table = 'apu_service_travel_estimation_dimensional_validation_contractors';
    protected $fillable = [
        'apu_service_dimensional_validation_contractors_id',
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
