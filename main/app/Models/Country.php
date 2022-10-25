<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'state',
        'iso',
        'dian_code',
        'code_phone',
    ];



    protected $table = 'countries';
}
