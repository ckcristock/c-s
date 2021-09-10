<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngressTypes extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'associated_account',
        'type',
        'status'
    ];
    protected $table = 'ingress_types';
}
