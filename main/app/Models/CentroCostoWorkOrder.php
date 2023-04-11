<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroCostoWorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'centro_costo_id',
    ];
}
