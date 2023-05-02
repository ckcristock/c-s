<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOfMemorandum extends Model
{
    use HasFactory;
    protected $table = 'type_of_memorandum';
    protected $fillable = [
        'name',
        'status'
    ];
}
