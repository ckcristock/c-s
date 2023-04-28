<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'work_time',
        'logo',
        'social_reason',
        'document_type',
        'document_number',
        'constitution_date',
        'email_contact',
        'phone',
        'verification_digit',
        'max_extras_hours',
        'max_holidays_legal',
        'max_late_arrival',
        'base_salary',
        'paid_operator',
        'transportation_assistance',
        'night_start_time',
        'night_end_time',
        'holidays',
        'payment_frequency',
        'account_number',
        'account_type',
        'payment_method',
        'law_1429',
        'law_590',
        'law_1607',
        'arl_id',
        'bank_id',
        'address',
        'page_heading',
        'commercial_terms',
        'technical_requirements',
        'legal_requirements'
    ];

    public function scopeWithWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }

    public function arl()
    {
        return $this->belongsTo(Arl::class);
    }

    public function bank()
    {
        return $this->belongsTo(Banks::class);
    }

    public function payConfiguration()
    {
        return $this->hasOne(PayConfigurationCompany::class);
    }

    public function payCompanyConfiguration()
    {
        return $this->hasOne(CompanyPaymentConfiguration::class, 'company_id', 'id');
    }
}
