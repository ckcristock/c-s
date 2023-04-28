<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'country_id',
        'department_id',
        'municipality_id',
        'dane_code',
        'dian_code',
        'percentage_product',
        'percentage_service',
        'state',
    ];

    protected $hidden = [
        "updated_at","created_at",
     ];


    public function routeTaxi()
    {
        return $this->hasMany(RouteTaxi::class);
    }
}
