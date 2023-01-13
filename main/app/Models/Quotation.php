<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'money_type',
        'customer_id',
        'destinity_id',
        'line',
        'trm',
        'project',
        'budget_included',
        'budget_id',
        'observation',
        'total_cop',
        'total_usd',
        'commercial_terms',
        'date',
        'code',
        'status',
        'client_id',
        'status',
    ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'destinity_id', 'id');
    }

    public function client()
    {
        return $this->hasOne(ThirdParty::class, 'id', 'customer_id')->name();
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class)->with('subItems');
    }

    public function scopeName($q)
    {
        return $q->select('*', DB::raw('CONCAT_WS(" - ", line, project) as name', 'id as value'));
    }


}
