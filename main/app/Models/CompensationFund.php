<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompensationFund extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'code', 'nit', 'status'];
    protected $table = 'compensation_funds';
}