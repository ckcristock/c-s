<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuPart extends Model
{
    use HasFactory;
	protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'city_id',
        'person_id',
        'user_id',
        'third_party_id',
        'line',
        'amount',
        'observation',
        'subtotal_raw_material',
        'commercial_materials_subtotal',
        'cut_water_total_amount',
        'cut_water_unit_subtotal',
        'cut_water_subtotal',
        'cut_laser_total_amount',
        'cut_laser_unit_subtotal',
        'cut_laser_subtotal',
        'machine_tools_subtotal',
        'internal_proccesses_subtotal',
        'external_proccesses_subtotal',
        'others_subtotal',
        'total_direct_cost',
        'unit_direct_cost',
        'indirect_cost_total',
        'direct_costs_indirect_costs_total',
        'direct_costs_indirect_costs_unit',
        'administrative_percentage',
        'administrative_value',
        'unforeseen_percentage',
        'unforeseen_value',
        'administrative_unforeseen_subtotal',
        'administrative_unforeseen_unit',
        'utility_percentage',
        'admin_unforeseen_utility_subtotal',
        'admin_unforeseen_utility_unit',
        'sale_price_cop_withholding_total',
        'sale_value_cop_unit',
        'trm',
        'sale_price_usd_withholding_total',
        'sale_value_usd_unit',
        'code',
        'state'
    ];

    // protected $hidden = [
    //     "updated_at","created_at",
    // ];

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

    public function user()
	{
		return $this->belongsTo(User::class)->with('person');
	}


    public function files()
	{
		return $this->hasMany(ApuPartFile::class);
	}

    public function rawmaterial()
	{
		return $this->hasMany(ApuPartRawMaterial::class);
	}

    public function commercial()
	{
		return $this->hasMany(ApuPartCommercialMaterial::class);
	}

    public function cutwater()
	{
		return $this->hasMany(ApuPartCutWater::class);
	}

    public function cutlaser()
	{
		return $this->hasMany(ApuPartCutLaser::class);
	}

    public function machine()
	{
		return $this->hasMany(ApuPartMachineTool::class);
	}

    public function external()
	{
		return $this->hasMany(ApuPartExternalProcess::class);
	}

    public function internal()
	{
		return $this->hasMany(ApuPartInternalProcess::class);
	}

    public function other()
	{
		return $this->hasMany(ApuPartOther::class);
	}

    public function indirect()
	{
		return $this->hasMany(ApuPartIndirectCost::class);
	}

}
