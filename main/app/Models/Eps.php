<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eps extends Model
{
    use HasFactory;
    protected $table = 'epss';
    protected $fillable = [
        'name',
        'nit',
        'code',
        'status'
    ];
}
