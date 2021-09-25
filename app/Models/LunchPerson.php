<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LunchPerson extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'lunch_id'
    ];

    public function lunch()
    {
        return $this->hasMany(Lunch::class);
    }
}
