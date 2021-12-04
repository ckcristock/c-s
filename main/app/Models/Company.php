<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
      'constitution_date',
      'document_number',
      'document_type',
      'email_contact',
      'phone',
      'social_reason',
      'verification_digit',
      'max_extras_hours',
      'max_holidays_legal',
      'max_late_arrival',
      'base_salary',
      'transportation_assistance',
      'night_start_time',
      'night_end_time',
      'holidays',
      'payment_frequency',
      'payment_method',
      'paid_operator',
      'law_1429',
      'law_590',
      'law_1607',
      'arl_id',
      'bank_id',
      'account_number',
      'account_type',
      'logo'
    ];

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

}
