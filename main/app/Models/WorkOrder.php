<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'referral_number',
        'invoice_number',
        'purchase_order',
        'name',
        'class',
        'type',
        'delivery_date',
        'expected_delivery_date',
        'delivery_date_of_plans',
        'date_of_plans_received',
        'date_of_referral',
        'date_of_invoice',
        'value',
        'third_party_id',
        'municipality_id',
        'third_party_person_id',
        'observations',
        'format_code',
        'description',
        'technical_requirements',
        'legal_requirements',
        'status',
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

    public function engineering()
    {
        return $this->hasOne(WorkOrderEngineering::class);
    }

    public function design()
    {
        return $this->hasOne(WorkOrderDesign::class);
    }

    public function elements()
    {
        return $this->hasMany(WorkOrderElement::class)->with('work_orderable');
    }
}
