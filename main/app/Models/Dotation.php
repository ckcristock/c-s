<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dotation extends Model
{
    use HasFactory;
    protected $fillable=[
        'dispatched_at',
        'person_id',
        'user_id',
        'description',
        'cost',
        'state',
        'type',
        'delivery_code',
        'delivery_state',
    ];

    public function dotation_products()
    {
        return $this->hasMany(DotationProduct::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->with('person');
    }
}
