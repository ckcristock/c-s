<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonWorkOrderDesign extends Model
{
    use HasFactory;
    protected $table = 'person_work_order_design';
    protected $fillable = [
        'person_id',
        'work_order_design_id'
    ];
}
