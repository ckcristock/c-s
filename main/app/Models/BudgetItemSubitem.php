<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetItemSubitem extends Model
{
    protected $guarded = ['id'];
    use HasFactory;

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
