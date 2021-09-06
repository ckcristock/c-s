<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memorandum extends Model
{
    use HasFactory;
    protected $fillable = [
        'person_id',
        'type_of_memorandum_id',
        'details',
        'file'
    ];
    protected $table = 'memorandum';
}
