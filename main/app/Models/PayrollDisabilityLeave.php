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
        'percentage',
    ];

}
