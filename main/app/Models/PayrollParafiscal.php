<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollParafiscal extends Model
{
    use HasFactory;

    protected $table = 'payroll_parafiscals';

    protected $fillable = [
        'prefix',
        'concept',
        'percentage',
    ];
}
