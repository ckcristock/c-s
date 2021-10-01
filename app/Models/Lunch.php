<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lunch extends Model
{
    use HasFactory;
    protected $fillable = [
        'person_id',
        'value'
    ];

    public function lunchPerson()
    {
        return $this->belongsTo(LunchPerson::class);
    }

    public function person(){
        return $this->belongsTo(Person::class);
    }
}
