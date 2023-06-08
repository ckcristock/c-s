<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderOrderManagement extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'number',
        'value',
        'file',
        'file_name',
        'date',
    ];
}
