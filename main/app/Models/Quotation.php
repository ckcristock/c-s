<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'money_type',
        'customer_id',
        'destinity_id',
        'line',
        'trm',
        'project',
        'date',
        'code',
        'client_id',
        'description',
        'status',
    ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'destinity_id', 'id');
    }

    public function client()
    {
        return $this->hasOne(ThirdParty::class, 'id', 'customer_id');
    }
}
