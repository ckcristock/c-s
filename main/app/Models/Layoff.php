<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layoff extends Model
{
    use HasFactory;

    protected $fillable = [
        'descirpcion',
        'total',
        'total_employees',
        'period',
        'payment_day',
        'status',
        'payer',
        'payer_id',
        'payer_fullname',
        'observations'
    ];

    public function layoffPerson(){
        return $this->hasMany(layoffPerson::class)->with('person');
    }

    public function personPayer()
    {
        return $this->belongsTo(Person::class, 'payer', 'id');
    }

}
