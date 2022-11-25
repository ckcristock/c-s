<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusPerson extends Model
{
    use HasFactory;

    protected $fillable = [
        'bonuses_id',
        'person_id',
        'identifier',
        'fullname',
        'worked_days',
        'amount',
        'lapse',
        'average_amount',
        'payment_date'
    ];
}
