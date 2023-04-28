<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'total_cost',
        'subtotal_indirect_cost',
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
        'value_prorrota_cop',
        'value_prorrota_usd',
        'unit_value_prorrateado_cop',
        'unit_value_prorrateado_usd',
        'budget_id',
    ];

    public  function subitems()
    {
        return $this->hasMany(BudgetItemSubitem::class);
    }
}
