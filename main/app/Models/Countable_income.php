<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Countable_income extends Model
{
    use HasFactory;

    protected $table = 'countable_income';

    protected $fillable = [
        'concept',
        'type',
        'accounting_account',
        'state',
        'editable'
    ];

    public function cuentaContable()
    {
        return $this->belongsTo(AccountPlan::class, 'accounting_account', 'Codigo_Niif');
    }

}
