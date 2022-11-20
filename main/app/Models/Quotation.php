<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'municipality_id',
        'code',
        'client_id',
        'description',
        'status',
    ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function client()
    {
        return $this->belongsTo(ThirdParty::class);
    }
}
