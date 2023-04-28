<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetItemSubitem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $fillable = [
        'budget_item_id',
        'type',
        'type_module',
        'description',
        'apu_part_id',
        'apu_set_id',
        'apu_service_id',
        'cuantity',
        'unit_cost',
        'total_cost',
        'subtotal_indirect_cost',
        'percentage_amd',
        'percentage_unforeseen',
        'percentage_utility',
        'value_amd',
        'value_unforeseen',
        'value_utility',
        'total_amd_imp_uti',
        'another_values',
        'subTotal',
        'retention',
        'percentage_sale',
        'value_cop',
        'value_usd',
        'unit_value_cop',
        'unit_value_usd',
        'unit',
        'value_prorrota_cop',
        'value_prorrota_usd',
        'unit_value_prorrateado_cop',
        'unit_value_prorrateado_usd',
        'observation',
    ];

    public  function indirect_costs()
    {
        return $this->hasMany(BudgetItemSubitemIndirectCost::class);
    }
    /**
     * Get the user that owns the BudgetItemSubitemIndirectCost
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function apuSet()
    {
        return $this->belongsTo(ApuSet::class);
    }

    /**
     * Get the user that owns the BudgetItemSubitemIndirectCost
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function apuPart()
    {
        return $this->belongsTo(ApuPart::class);
    }


    public function apuService()
    {
        return $this->belongsTo(ApuService::class);
    }
}
