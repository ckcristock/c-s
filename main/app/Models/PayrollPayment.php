<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollPayment extends Model
{

    use HasFactory;

   protected $fillable = [
/*     "total_cost",
    "end_period",
    "start_period",
    "total_overtimes_surcharges",
    "total_incomes",
    "total_parafiscals",
    "total_provisions",
    "total_retentions",
    "total_salaries",
    "total_social_secturity",
    "payment_frequency",
    "company_id",
    */

    "user_id",
    "code",
    "payment_frequency",
    "start_period",
    "end_period",
    "total_salaries",
    "total_retentions",
    "total_provisions",
    "total_social_secturity",
    "total_parafiscals",
    "total_overtimes_surcharges",
    "total_incomes",
    "total_cost",
    "created_at",
    "updated_at",
    "electronic_reported",
    "electronic_reported_date",
    "user_electronic_reported",
    "company_id",
   ];

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
