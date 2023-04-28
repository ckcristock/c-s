<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annotation extends Model
{
    use HasFactory;
    protected $fillable = [
        'description',
        'disciplinary_process_id',
        'file',
        'user_id'
    ];
}
