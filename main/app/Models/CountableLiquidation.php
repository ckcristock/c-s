<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountableLiquidation extends Model
{
    use HasFactory;

    protected $fillable = [
    'concept',
    'account_plan_id',
    'status'
    ];

    public function cuentaContable()
    {
        return $this->belongsTo(AccountPlan::class, 'account_plan_id', 'Codigo_Niif');
    }

}
