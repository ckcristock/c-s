<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PensionFund extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'nit', 'code', 'status'];
    protected $table = 'pension_funds';
}
