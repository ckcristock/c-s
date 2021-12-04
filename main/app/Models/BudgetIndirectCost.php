<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BudgetIndirectCost extends Model
{
    protected $guarded = ['id'];
    
    use HasFactory;

    /**
     * Get the user that owns the BudgetIndirectCost
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function indirectCost()
    {
        return $this->belongsTo(IndirectCost::class);
    }
   
}
