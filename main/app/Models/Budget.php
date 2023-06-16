<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Budget extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $fillable = [
        'code',
        'format_code',
        'user_id',
        'customer_id',
        'destinity_id',
        'line',
        'trm',
        'project',
        'observation',
        'total_cop',
        'total_usd',
        'internal_total',
        'unit_value_prorrateado_cop',
        'state',
        'unit_value_prorrateado_usd',
    ];

    protected $appends = ['total_indirect_cost', 'total_direct_cost'];

    public function getTotalIndirectCostAttribute()
    {
        return $this->items->sum('subtotal_indirect_cost');
    }

    public function getTotalDirectCostAttribute()
    {
        return $this->items->sum('total_cost');
    }

    public function customer()
    {
        return $this->belongsTo(ThirdParty::class, 'customer_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class)->with('person');
    }
    public function destiny()
    {
        return $this->belongsTo(Municipality::class, 'destinity_id', 'id');
    }
    public function items()
    {
        return $this->hasMany(BudgetItem::class)->with('subitems');
    }
    public function indirectCosts()
    {
        return $this->hasMany(BudgetIndirectCost::class);
    }
    public function scopeName($q)
    {
        return $q->select('*', DB::raw('CONCAT_WS("-", line, project) as name'));
    }
    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
}
