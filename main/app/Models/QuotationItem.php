<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable= [
        'name',
        'value_cop',
        'value_usd',
        'total_cop',
        'total_usd',
        'quotation_id',
        'cuantity',
        'quotationitemable_id',
        'quotationitemable_type',
    ];

    public function subItems()
    {
        return $this->hasMany(QuotationItemSubitem::class);
    }

    public function quotationitemable()
    {
        return $this->morphTo();
    }

}
