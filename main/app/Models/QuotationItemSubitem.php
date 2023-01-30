<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItemSubitem extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'cuantity',
        'value_cop',
        'value_usd',
        'total_cop',
        'total_usd',
        'quotation_item_id',
        'budget_item_subitem_id',
    ];
}
