<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApuSet extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "city_id",
        "person_id",
        "third_party_id",
        "line",
        "observation",
        "list_pieces_sets_subtotal",
        "machine_tools_subtotal",
        "internal_processes_subtotal",
        "external_processes_subtotal",
        "others_subtotal",
        "total_direct_cost",
        "unit_direct_cost",
        "indirect_cost_total",
        "direct_costs_indirect_costs_total",
        "administrative_percentage",
        "administrative_value",
        "unforeseen_percentage",
        "unforeseen_value",
        "administrative_unforeseen_subtotal",
        "administrative_unforeseen_unit",
        "utility_percentage",
        "admin_unforeseen_utility_subtotal",
        "sale_price_cop_withholding_total",
        "trm",
        "sale_price_usd_withholding_total",
        "code",
        'format_code',
        "state",
        'typeapu_name',
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
                "apu_set" as type_module,
                "C" as type,
                "Conjunto" as type_name,
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
                total_direct_cost as unit_cost,
                third_party_id,
                set_name,
                machine_name
            ')
        )->when($request->code, function ($q, $fill) {
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
                $q->whereBetween('created_at', [$request->date_one, $request->date_two])
                    ->orWhereDate('created_at', date($request->date_one))
                    ->orWhereDate('created_at', date($request->date_two));
            });
    }

    public function machine()
    {
        return $this->hasMany(ApuSetMachineTool::class)->with('machine', 'unit');
    }

    public function setpartlist()
    {
        return $this->hasMany(ApuSetPartList::class)->with('unit');
    }

    public function external()
    {
        return $this->hasMany(ApuSetExternalProcess::class)->with('external', 'unit');
    }

    public function internal()
    {
        return $this->hasMany(ApuSetInternalProcess::class)->with('internal', 'unit');
    }

    public function other()
    {
        return $this->hasMany(ApuSetOther::class)->with('unit');
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
