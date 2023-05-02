<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventaryDotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_dotation_type_id',
        'name',
        'code',
        'type',
        'status',
        'cost',
        'stock',
        'size',
    ];

    public function dotacionProducto(){
        return $this->hasMany(DotationProduct::class);
    }

    public function product_datation_types(){
        return $this->hasOne(ProductDotationType::class, 'id', 'product_dotation_type_id');
    }
}
