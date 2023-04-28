<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'product_id',
        'name',
        'ammount',
        'status'
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'Id_Producto')->with('unit');
    }

    public function quotation() {
        return $this->hasMany(QuotationPurchaseRequest::class, 'product_purchase_request_id', 'id');
    }



}
