<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetItemSubitemIndirectCost extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $fillable = [
        'budget_item_subitem_id',
        'indirect_cost_id',
        'value'
    ];
}
