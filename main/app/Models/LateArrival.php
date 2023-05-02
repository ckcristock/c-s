<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LateArrival extends Model
{
    use HasFactory;
    protected $fillable = [
        'person_id',
        'time',
        'entry',
        'real_entry',
        'count',
        'justification',
        'date',
    ];

     /**
     * Una llegada tarde pertenece a un centro de costo
     *
     * @return void
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
