<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisabilityLeave extends Model
{
    use HasFactory;
    protected $fillable = [
        'concept',
        'accounting_account',
        'sum',
        'state',
        'novelty',
        'modality'
    ];
}
