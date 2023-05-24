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
    ];

    public function work_orderable(): MorphTo
    {
        return $this->morphTo();
    }
}
