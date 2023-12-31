<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PensionFund extends Model
{
    use HasFactory;
    protected $table = 'pension_funds';
    protected $fillable = [
        'name',
        'code',
        'nit',
        'status'
    ];
}
