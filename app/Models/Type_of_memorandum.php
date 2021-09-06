<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type_of_memorandum extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'status'];
    protected $table = 'type_of_memorandum';
}
