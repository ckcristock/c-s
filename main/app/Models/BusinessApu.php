<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessApu extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'apuable_id',
        'apuable_type'
    ];

    public function apuable()
    {
        return $this->morphTo();
    }
}
