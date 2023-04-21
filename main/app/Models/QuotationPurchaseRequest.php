<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationPurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',    
        'code',
        'format_code',
        'Id_Proveedor',    
        'total_price',
        'status',
        'file',                
    ];
}
