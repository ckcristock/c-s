<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayoffPerson extends Model
{
    use HasFactory;

    protected  $fillable = [
        'layoffs_id',
        'person_id',
        'identifier',
        'fullname',
        'worked_days',
        'period',
        'salary_basic',
        'avg_salary_basic',
        'total',
        'payment_date',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'id', 'person_id');
    }
}
