<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RrhhActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'name',
        'user_id',
        'date_end',
        'date_start',
        'hour_start',
        'hour_end',
        'state',
        'rrhh_activity_type_id',
        'dependency_id',
        'code'
    ];


    public function peopleActivity()
    {
        return $this->hasMany(RrhhActivityPerson::class);
    }
}
