<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReasonWithdrawal extends Model
{
    use HasFactory;
    protected $table = 'reason_withdrawal';
    protected $fillable = [
        'name',
        'requirements',
    ];
}
