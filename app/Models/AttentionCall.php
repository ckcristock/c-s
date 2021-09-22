<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttentionCall extends Model
{
    use HasFactory;
    protected $fillable = [
        'reason',
        'number_call',
        'person_id',
        'user_id'
    ];
}
