<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderBlueprint extends Model
{
    use HasFactory;

    protected $fillable =[
        'file',
        'general_set',
        'set_name',
        'predetermined',
        'work_order_id',
        'person_id',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function work_order()
    {
        return $this->belongsTo(WorkOrder::class);
    }
}
