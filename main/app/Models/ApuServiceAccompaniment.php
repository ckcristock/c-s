<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuServiceAccompaniment extends Model
{
    use HasFactory;

    protected $fillable = [
        'apu_service_id',
        'apu_service_dimensional_validation_id',
        'apu_profile_id',
        'displacement_type',
        'observation',
        'days_number_displacement',
        'days_number_festive',
        'days_number_ordinary',
        'hours_value_displacement',
        'hours_value_festive',
        'hours_value_ordinary',
        'hours_festive',
        'hours_displacement',
        'hours_ordinary',
        'people_number',
        'salary_value',
        'subtotal',
        'total_value_displacement',
        'total_value_festive',
        'total_value_ordinary',
        'workind_day_displacement',
        'working_day_festive',
        'working_day_ordinary'
    ];

    public function travelEstimationAccompaniment()
    {
        return $this->hasMany(
            ApuServiceTravelEstimationAccompaniment::class,
            'apu_service_accompaniments_id'
        )
            ->with('travelExpenseEstimation')
            ->orderBy('travel_expense_estimation_id');
    }

    public function profiles()
    {
        return $this->belongsTo(ApuProfile::class, 'apu_profile_id');
    }
}
