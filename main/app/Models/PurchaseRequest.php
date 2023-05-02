<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{

    use HasFactory;

    protected $fillable = [
        'category_id',
        'expected_date',
        'observations',
        'quantity_of_products',
        'status',
        'code',
        'format_code',
        'user_id',
    ];

    public function productPurchaseRequest()
    {
        return $this->hasMany(ProductPurchaseRequest::class)->with('product', 'quotation');
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'user_id')->fullName()->with('contractultimate');
    }

    public function quotationPurchaseRequest()
    {
        return $this->hasMany(QuotationPurchaseRequest::class, 'purchase_request_id', 'id' );
    }

    public function activity()
    {
        return $this->hasMany(PurchaseRequestActivity::class,'id_purchase_request', 'id');
    }

}
