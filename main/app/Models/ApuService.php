<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuService extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'line', 
        'observation',
        'person_id',
        'city_id',
        'third_party_id',
        'administrative_percentage',
        'administrative_value',
        'unforeseen_percentage',
        'unforeseen_value',
        'utility_percentage',
        'general_subtotal_travel_expense_labor',
        'sale_price_cop_withholding_total',
        'sale_price_usd_withholding_total',
        'subtotal_administrative_unforeseen',
        'subtotal_administrative_unforeseen_utility',
        'subtotal_assembly_commissioning',
        'subtotal_dimensional_validation',
        'subtotal_labor',
        'subtotal_labor_mpm',
        'subtotal_travel_expense',
        'subtotal_travel_expense_mpm',
        'trm',
        'code'
    ];

    public function city()
	{
		return $this->belongsTo(City::class);
	}

    public function person()
	{
		return $this->belongsTo(Person::class);
	}

    public function thirdParty()
	{
		return $this->belongsTo(ThirdParty::class);
	}

    public function dimensionalValidation()
	{
		return $this->hasMany(ApuServiceDimensionalValidation::class);
	}

    public function assembliesStartUp()
	{
		return $this->hasMany(ApuServiceAssemblyStartUp::class);
	}

}
