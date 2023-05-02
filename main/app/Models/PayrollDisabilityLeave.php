<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollDisabilityLeave extends Model
{

    use HasFactory;

    protected $table = 'payroll_disability_leaves';

    protected $fillable = [
        'prefix',
        'concept',
        'account_plan_id',
        'percentage',
    ];

    public function cuentaContable()
    {
        return $this->belongsTo(AccountPlan::class, 'account_plan_id', 'Codigo_Niif');
    }

}
