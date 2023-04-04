<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeveranceInterestPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'total',
        'total_employees',
        'user_id',
        'type',
        'company_id'
    ];

    public function user(){
        return $this->belongsTo(User::class)->with('personName')->select('person_id', 'id');
    }

    public function people(){
        return $this->hasMany(SeveranceInterestPaymentPerson::class)->with('person');
    }
}
