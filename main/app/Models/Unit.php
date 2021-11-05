<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        "name"
    ];

    protected $hidden = [
        "updated_at","created_at",
    ];


    public function machine(){
        return $this->hasMany(Machine::class);
    }
    public function internalProcess(){
        return $this->hasMany(Machine::class);
    }
    public function externalProcess(){
        return $this->hasMany(Machine::class);
    }
    public function other(){
        return $this->hasMany(Other::class);
    }
}
