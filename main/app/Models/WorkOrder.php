<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order',
        'quotation_id',
        'delivery_date',
        'date',
        'third_party_id',
        'municipality_id',
        'required_by',
        'observation',
        'code',
        'description',
        'technical_requirements',
        'legal_requirements',
        'status',
    ];

    public function city()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function third_party()
    {
        return $this->belongsTo(ThirdParty::class)->name();
    }
}
