<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marcation extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'description' ,
        'date',
        'img' ,
        'person_id'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class)->onlyName();
    }
}
