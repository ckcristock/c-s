<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderEngineering extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id',
        'allocator_person_id',
        'work_order_id',
        'observations',
        'hours',
        'minutes',
        'status',
    ];
}
