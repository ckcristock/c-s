<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DotationProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'dotation_id',
        'inventary_dotation_id',
        'quantity',
        'cost',
        'code',
    ];

    public function inventary_dotation()
    {
        return $this->belongsTo(InventaryDotation::class);
    }
}
