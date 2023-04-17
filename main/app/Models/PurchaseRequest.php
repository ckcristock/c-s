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
        'status',
        'quantity_of_products'    
    ];

    public function productPurchaseRequest()
    {
        return $this->hasMany(ProductPurchaseRequest::class);
    }

}
