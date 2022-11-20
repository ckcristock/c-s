<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyPaymentConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'calculate_work_disability',
        'pay_deductions',
        'recurring_payment',
        'payment_transport_subsidy',
        'affects_transportation_subsidy',
        'pay_vacations',
        'company_id'
    ];

    public function companyConfPayment()
    {
        return $this->belongsTo(Company::class, 'id', 'company_id');
    }
}
