<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuServiceDimensionalValidationContractors extends Model
{
    use HasFactory;

    protected $table = 'apu_service_dimensional_validation_contractors';

    protected $fillable = [
        'apu_service_id',
        'apu_profile_id',
        'displacement_type',
        'observation',
        'days_number_displacement',
        'days_number_festive',
        'days_number_ordinary',
        'hours_displacement',
        'hours_festive',
        'hours_ordinary',
        'people_number',
        'subtotal',
        'workind_day_displacement',
        'working_day_festive',
        'working_day_ordinary'
    ];

    public function travelEstimationDimensionalValidationsC()
    {
        return $this->hasMany(
            ApuServiceTravelEstimationDimensionalValidationContractors::class,
            'apu_service_dimensional_validation_contractors_id',
            'id'
        )
            ->with('travelExpenseEstimation')
            ->orderBy('travel_expense_estimation_id');
    }

    public function profiles()
    {
        return $this->belongsTo(ApuProfile::class, 'apu_profile_id');
    }
}
