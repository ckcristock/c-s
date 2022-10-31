<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollRisksArl extends Model
{
    use HasFactory;

    protected $table = 'payroll_risks_arls';

    protected $fillable = [
        'prefix',
        'concept',
        'percentage',
    ];

}
