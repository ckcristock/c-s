<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'minute_value_laser',
        'minute_value_water',
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
        'format_code',
        'state',
        'set_name',
        'machine_name',
    ];

    // protected $hidden = [
    //     "updated_at","created_at",
    // ];

    public function city()
    {
        return $this->belongsTo(Municipality::class, 'city_id', 'id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class)->name();
    }

    public function thirdParty()
    {
        return $this->belongsTo(ThirdParty::class)->name();
    }

    public function scopeExtra($q, $request)
    {
        //dd($request);
        return $q->select(
            DB::raw(
                '"apu_part" as type_module,
                "P" as type,
                "Pieza" as type_name,
                false as selected,
                id as apu_id,
                name,
                code,
                observation,
                line,
                city_id,
                created_at,
                person_id,
                typeapu_name,
                unit_direct_cost as unit_cost,
                third_party_id,
                set_name,
                machine_name'
            )
        )
            ->when($request->code, function ($q, $fill) {
                $q->where('code', 'like', "%$fill%");
            })
            ->when($request->name, function ($q, $fill) {
                $q->where('name', 'like', "%$fill%");
            })
            ->when($request->line, function ($q, $fill) {
                $q->where('line', 'like', "%$fill%");
            })
            ->when($request->description, function ($q, $fill) {
                $q->where('observation', 'like', "%$fill%");
            })
            ->when($request->type, function ($q, $fill) {
                $q->where('typeapu_name', $fill);
            })
            ->when($request->set_name, function ($q, $fill) {
                $q->where('set_name', 'like', "%$fill%");
            })
            ->when($request->machine_name, function ($q, $fill) {
                $q->where('machine_name', 'like', "%$fill%");
            })
            ->when($request->date_one, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->whereBetween('created_at', [$request->date_one, $request->date_two])
                          ->orWhereDate('created_at', date($request->date_one))
                          ->orWhereDate('created_at', date($request->date_two));
                });
            });
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
        return $this->hasMany(ApuPartMachineTool::class)->with('machine');
    }

    public function external()
    {
        return $this->hasMany(ApuPartExternalProcess::class)->with('external');
    }

    public function internal()
    {
        return $this->hasMany(ApuPartInternalProcess::class)->with('internal');
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
