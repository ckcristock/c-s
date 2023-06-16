<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderQuotationSubitem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_item_subitem_id',
        'name',
        'cuantity',
        'unit',
        'observations'
    ];

    public function quotationSubitem()
    {
        return $this->belongsTo(QuotationItemSubitem::class, 'quotation_item_subitem_id', 'id');
    }
}
