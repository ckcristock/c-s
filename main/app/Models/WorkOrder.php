<?php

namespace App\Models;

use Carbon\Carbon;
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
        'total_budgets',
        'total_apu_parts',
        'total_apu_sets',
        'total_apu_services',
        'total_budget_part_set_service',
        'total_order_managment',
    ];

    protected $appends = [
        'real_days',
        'committed_days',
        'delivery_days',
        'status_time',
        'indicator',
        'design_days',
        'design_percentage',
        'production_days',
    ];

    public function getIndicatorAttribute()
    {
        $fechaEntregaReal = $this->attributes['delivery_date'];
        $fechaEntrega = $this->attributes['expected_delivery_date'];

        if ($fechaEntregaReal < $fechaEntrega) {
            return 'CUMPLE';
        } else {
            return 'NO CUMPLE';
        }
    }

    public function getProductionDaysAttribute() {
        if (!$this->attributes['date_of_plans_received']) {
            return 'N/A';
        }
        $fechaEntrega = Carbon::parse($this->attributes['expected_delivery_date']);
        $fechaPlanosRecibidos = Carbon::parse($this->attributes['date_of_plans_received']);

        return $fechaEntrega - $fechaPlanosRecibidos;
    }

    public function getDesignPercentageAttribute()
    {
        $designDays = $this->getDesignDaysAttribute();
        $committedDays = $this->getCommittedDaysAttribute();

        if (!is_numeric($designDays) || !is_numeric($committedDays) || $committedDays === 0) {
            return 'N/A';
        }

        return ($designDays / $committedDays) * 100;
    }

    public function getDesignDaysAttribute()
    {
        $fechaCreacion = $this->attributes['created_at'];
        $fechaEntrega = $this->attributes['delivery_date_of_plans'];

        if (!$fechaEntrega) {
            return 'N/A';
        }

        $diffTime = strtotime($fechaEntrega) - strtotime($fechaCreacion);
        $diffDays = ceil($diffTime / (24 * 60 * 60));

        return $diffDays + 1;
    }

    public function getRealDaysAttribute()
    {
        $fechaEntregaReal = $this->attributes['delivery_date'];
        $fechaCreacion = $this->attributes['created_at'];

        if (!$fechaEntregaReal) {
            return 'N/A';
        }

        $fechaEntregaReal = Carbon::parse($fechaEntregaReal);
        $fechaCreacion = Carbon::parse($fechaCreacion);
        $differenceInDays = $fechaEntregaReal->diffInDays($fechaCreacion) + 1;

        return $differenceInDays;
    }

    public function getDeliveryDaysAttribute()
    {
        $fechaEntrega = Carbon::parse($this->attributes['expected_delivery_date']);
        $today = Carbon::now()->setTime(0, 0, 0);

        $diffDays = $fechaEntrega->diffInDays($today);

        return $diffDays >= 0 ? $diffDays : '';
    }

    public function getCommittedDaysAttribute()
    {
        $fechaEntrega = $this->attributes['expected_delivery_date'];
        $fechaCreacion = $this->attributes['created_at'];

        if (!$fechaEntrega) {
            return 'N/A';
        }

        $fechaEntrega = Carbon::parse($fechaEntrega);
        $fechaCreacion = Carbon::parse($fechaCreacion);
        $committedDays = $fechaEntrega->diffInDays($fechaCreacion) + 1;

        return $committedDays;
    }

    public function getStatusTimeAttribute()
    {
        $estado = $this->attributes['status'];
        $diasRestantes = $this->getDeliveryDaysAttribute();

        if ($estado === 'T') {
            return 'TERMINADO';
        }

        if ($estado === 'A') {
            return 'NA';
        }

        if ($diasRestantes > 0) {
            return 'A TIEMPO';
        }

        if ($diasRestantes <= 0) {
            return 'RETRASADO';
        }

        return '';
    }

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

    public function order_managments()
    {
        return $this->hasMany(WorkOrderOrderManagement::class);
    }
}
