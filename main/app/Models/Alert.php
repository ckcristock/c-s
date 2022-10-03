<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;
    protected $fillable = [
        'person_id',
        'title',
        'modal',
        'user_id',
        'type',
        'icon',
        'description',
        'url',
        'destination_id',
    ];
}
