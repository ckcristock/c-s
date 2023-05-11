<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuServiceAssemblyStartUp extends Model
{
    use HasFactory;
    protected $fillable = [
        'apu_service_id',
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

    public function travelEstimationAssembliesStartUp()
    {
        return $this->hasMany(
            ApuServiceTravelEstimationAssemblyStartUp::class,
            'apu_service_assembly_start_up_id'
        )
            ->with('travelExpenseEstimation')
            ->orderBy('travel_expense_estimation_id');
    }

    public function profiles()
    {
        return $this->belongsTo(ApuProfile::class, 'apu_profile_id');
    }
}
