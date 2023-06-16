<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderQuotationItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'quotation_item_id',
        'name',
        'cuantity',
        'unit',
        'observations'
    ];

    public function subItems()
    {
        return $this->hasMany(WorkOrderQuotationSubitem::class);
    }

    public function quotationItem()
    {
        return $this->belongsTo(QuotationItem::class);
    }

}
