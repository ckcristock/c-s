<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApuSet extends Model
{
    use HasFactory;

    protected $fillable = [

        "name" ,
        "city_id" ,
        "person_id" ,
        "third_party_id" ,
        "line" ,
        "observation" ,
        "list_pieces_sets_subtotal" ,
        "machine_tools_subtotal" ,
        "internal_processes_subtotal" ,
        "external_processes_subtotal" ,
        "others_subtotal" ,
        "total_direct_cost" ,
        "unit_direct_cost" ,
        "indirect_cost_total" ,
        "direct_costs_indirect_costs_total" ,
        "administrative_percentage" ,
        "administrative_value" ,
        "unforeseen_percentage" ,
        "unforeseen_value" ,
        "administrative_unforeseen_subtotal" ,
        "administrative_unforeseen_unit" ,
        "utility_percentage" ,
        "admin_unforeseen_utility_subtotal" ,
        "sale_price_cop_withholding_total" ,
        "trm" ,
        "sale_price_usd_withholding_total",
        "code",
        "state"
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

    public function machine()
	{
		return $this->hasMany(ApuSetMachineTool::class);
	}

    public function setpartlist()
	{
		return $this->hasMany(ApuSetPartList::class);
        
	}

    public function external()
	{
		return $this->hasMany(ApuSetExternalProcess::class);
	}

    public function internal()
	{
		return $this->hasMany(ApuSetInternalProcess::class);
	}

    public function other()
	{
		return $this->hasMany(ApuSetOther::class);
	}

    public function indirect()
	{
		return $this->hasMany(ApuSetIndirectCost::class);
	}

    public function files()
	{
		return $this->hasMany(ApuSetFile::class);
	}

}
