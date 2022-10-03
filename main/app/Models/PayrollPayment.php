<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollPayment extends Model
{
   protected $fillable = [
    "total_cost",
    "end_period",
    "start_period",
    "total_overtimes_surcharges",
    "total_incomes",
    "total_parafiscals",
    "total_provisions",
    "total_retentions",
    "total_salaries",
    "total_social_secturity",
    "payment_frequency"
   ];
       

    use HasFactory;

    public function personPayrollPayment()
    {
        return $this->hasMany(PersonPayrollPayment::class);
    }

    public function provisionsPersonPayrollPayment()
    {
        return $this->hasMany(ProvisionsPersonPayrollPayment::class);
    }

    public function socialSecurityPersonPayrollPayment()
    {
        return $this->hasMany(SocialSecurityPersonPayrollPayment::class);
    }

    public function scopeVacacionesAcumuladasFuncionarioWithId($query, $id)
    {
        return $query->select('id', 'start_period', 'end_period')->orderByRaw('start_period DESC, end_period DESC')->with(['provisionsPersonPayrollPayment' => function ($query) use ($id) {
            $query->select('id', 'person_id', 'payroll_payment_id', 'accumulated_vacations')->where('person_id', $id);
        }]);
    }

}
