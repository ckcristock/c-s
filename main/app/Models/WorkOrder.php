<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'purchase_order',
        'quotation_id',
        'delivery_date',
        'date',
        'third_party_id',
        'municipality_id',
        'third_party_person_id',
        'observation',
        'code',
        'description',
        'technical_requirements',
        'legal_requirements',
        'status',
        'type'
    ];

    public function city()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }

    public function third_party()
    {
        return $this->belongsTo(ThirdParty::class)->name2();
    }

    public function third_party_person()
    {
        return $this->belongsTo(ThirdPartyPerson::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class)->name();
    }

    public function blueprints()
    {
        return $this->hasMany(WorkOrderBlueprint::class)->with('person');
    }
}
