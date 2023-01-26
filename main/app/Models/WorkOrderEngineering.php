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
        'start_time',
        'end_time',
        'status',
    ];

    public function work_order()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class)->name();
    }

    public function allocator_person()
    {
        return $this->hasOne(Person::class, 'id', 'allocator_person_id')->name();
    }
}
