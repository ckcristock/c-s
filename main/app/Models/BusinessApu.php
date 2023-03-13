<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessApu extends Model
{
    use HasFactory;

    protected $fillable = [
        'apuable_id',
        'apuable_type',
        'business_id',
        'status'
    ];

    public function apuable()
    {
        return $this->morphTo();
    }

}
