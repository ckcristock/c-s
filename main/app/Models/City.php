<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'type', 'country_id', 'state'
    ];

    protected $hidden = [
        "updated_at","created_at",
     ];


    public function routeTaxi()
    {
        return $this->hasMany(RouteTaxi::class);
    }
}
