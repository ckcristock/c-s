<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Budget extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

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
}
