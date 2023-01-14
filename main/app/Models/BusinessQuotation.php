<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessQuotation extends Model
{
    use HasFactory;
    protected $table = 'business_quotation';
    protected $fillable = [
        'id',
        'business_id',
        'quotation_id',
        'status'
    ];
}
