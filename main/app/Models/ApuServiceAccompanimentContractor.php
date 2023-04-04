<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuServiceAccompanimentContractor extends Model
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
        'hours_displacement',
        'hours_festive',
        'hours_ordinary',
        'people_number',
        'subtotal',
        'workind_day_displacement',
        'working_day_festive',
        'working_day_ordinary'
    ];

    public function travelEstimationAccompanimentC()
	{
		return $this->hasMany(ApuServiceTravelEstimationAccompanimentContractor::class, 'apu_service_accompaniment_contractors_id')->with('travelExpenseEstimation');
	}

    public function profiles()
    {
        return $this->belongsTo(ApuProfile::class, 'apu_profile_id');
    }
}
