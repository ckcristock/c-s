<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disciplinary_process extends Model
{
    use HasFactory;
    protected $fillable  = [
        'person_id',
        'process_description',
        'date_of_admission',
        'date_end',
        'status'
    ];
    protected $table = 'disciplinary_process';
}
