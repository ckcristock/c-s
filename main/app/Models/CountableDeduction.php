<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountableDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'concept',
        'accounting_account',
        'state',
        'editable'
    ];

    public function cuentaContable()
    {
        return $this->belongsTo(AccountPlan::class, 'accounting_account', 'Codigo_Niif');
    }
    
}
