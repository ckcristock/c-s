<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeveranceInterestPaymentPerson extends Model
{
    use HasFactory;
    protected $fillable = [
        'severance_interest_payment_id',
        'person_id',
        'total',
        'company_id',
    ];

    public function person() {
        return $this->belongsTo(Person::class)->onlyName();
    }
}
