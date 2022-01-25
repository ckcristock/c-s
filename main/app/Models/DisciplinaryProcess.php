<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplinaryProcess extends Model
{
    use HasFactory;
    protected $fillable  = [
        'person_id',
        'process_description',
        'date_of_admission',
        'date_end',
        'status',
        'file',
        'approve_user_id'
    ];
}
