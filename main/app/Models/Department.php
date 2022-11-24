<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'country_id',
        'dian_code',
        'dane_code',
    ];

    public function municipalities()
    {
        return $this->hasMany(Municipality::class)->orderBy('name');
    }
}
