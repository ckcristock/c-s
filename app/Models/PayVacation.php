<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayVacation extends Model
{
    use HasFactory;
    protected $fillable = [
        'payroll_factor_id',
        'state',
        'value',
        'days'
    ];
}
