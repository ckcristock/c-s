<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectronicPayroll extends Model
{
    use HasFactory;
    protected $fillable = [
        'status',
        'person_payroll_payment_id',
        'cune',
        'errors',
        'message',
        'code',
    ];
}
