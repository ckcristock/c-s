<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'general_subtotal_travel_expense_labor_c',
        'total_unit_cost',
        'sale_price_cop_withholding_total',
        'sale_price_usd_withholding_total',
        'subtotal_administrative_unforeseen',
        'subtotal_administrative_unforeseen_utility',
        'subtotal_assembly_commissioning',
        'subtotal_accompaniment',
        'subtotal_dimensional_validation',
        'subtotal_dimensional_validation_c',
        'subtotal_assembly_c',
        'subtotal_accompaniment_c',
        'subtotal_labor',
        'subtotal_labor_mpm',
        'subtotal_labor_apm',
        'subtotal_travel_expense',
        'subtotal_travel_expense_mpm',
        'subtotal_travel_expense_apm',
        'subtotal_travel_expense_vd_c',
        'subtotal_travel_expense_me_c',
        'subtotal_travel_expense_apm_c',
        'trm',
        'code',
        'format_code',
        'set_name',
        'machine_name',
    ];

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
        return $q->select(
            DB::raw('
                "apu_service" as type_module,
                "S" as type,
                "Servicio" as type_name,
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
                total_unit_cost as unit_cost,
                third_party_id,
                set_name,
                machine_name
            ')
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
            ->when($request->date_one, function ($q) use($request) {
                $q->whereBetween('created_at', [$request->date_one, $request->date_two])
                ->orWhereDate('created_at', date($request->date_one))
                ->orWhereDate('created_at', date($request->date_two));
            });
    }

    public function dimensionalValidation()
    {
        return $this->hasMany(ApuServiceDimensionalValidation::class);
    }

    public function assembliesStartUp()
    {
        return $this->hasMany(ApuServiceAssemblyStartUp::class);
    }

    public function accompaniments()
    {
        return $this->hasMany(ApuServiceAccompaniment::class);
    }

    public function dimensionalValidationC()
    {
        return $this->hasMany(ApuServiceDimensionalValidationContractors::class);
    }

    public function assembliesStartUpC()
    {
        return $this->hasMany(ApuServiceAssemblyEquipmentContractor::class);
    }

    public function accompanimentsC()
    {
        return $this->hasMany(ApuServiceAccompanimentContractor::class);
    }
}
