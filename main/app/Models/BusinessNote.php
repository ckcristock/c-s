<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'person_id',
        'note',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class)->name();
    }
}
