<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSocialSecurityPerson extends Model
{
    use HasFactory;

    protected $fillable = [
        'prefix',
        'concept',
        'percentage',
        'account_plan_id',
        'account_setoff'
    ];

    public function cuentaContable()
    {
        return $this->belongsTo(AccountPlan::class, 'account_plan_id', 'Codigo_Niif');
    }

    public function contrapartida()
    {
        return $this->belongsTo(AccountPlan::class, 'account_setoff', 'Codigo_Niif');
    }

}
