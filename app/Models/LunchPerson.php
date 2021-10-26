<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LunchPerson extends Model
{
    use HasFactory;
    protected $fillable = [
        'person_id', 
        'lunch_id',
        'state'
    ];

    public function lunch()
    {
        return $this->hasMany(Lunch::class);
    }

    public function person(){
        return $this->belongsTo(Person::class);
    }
}
