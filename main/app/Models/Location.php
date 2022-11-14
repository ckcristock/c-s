<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $fillable =  [
        'name',
        'code',
        'address',
        'agreements',
        'category',
        'city',
        'country_code',
        'creation_date',
        'disabled',
        'email',
        'encoding_characters',
        'interface_id',
        'logo',
        'pbx',
        'name',
    ];
}
