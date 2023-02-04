<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayoffsCertificate extends Model
{
    use HasFactory;
    protected $fillable = [
        'reason_withdrawal',
        'person_id',
        'reason',
        'document',
        'monto',
        'valormonto',
        'state'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class)->with('severance_fund')->name();
    }

    public function reason_withdrawal_list()
    {
        return $this->belongsTo(ReasonWithdrawal::class, 'reason_withdrawal');
    }
}
