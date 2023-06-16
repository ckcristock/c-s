<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WorkOrderElement extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'work_orderable_id',
        'work_orderable_type',
        'total_indirect_cost',
        'total_direct_cost',
        'total',
    ];

    public function work_orderable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getGroupedByCategoriaAttribute()
    {
        return $this->all()->groupBy('work_orderable_type');
    }
}
