<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DotationProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'quantity',
        'inventary_dotation_id',
        'cost',
        'code',
        'dotation_id',
    ];

    public function inventary_dotation()
    {
        return $this->belongsTo(InventaryDotation::class);
    }
}
