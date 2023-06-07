<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Responsible extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'person_id'];

    public function person()
    {
        return $this->belongsTo(Person::class)->fullName();
    }
}
