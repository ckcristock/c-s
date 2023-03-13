<?php

namespace App\Models;

use App\Http\Controllers\LateArrivalController;
use App\Services\RotatingHourService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Person extends Model
{
    protected $guarded = [''];

    protected $fillable = [
        'blood_type',
        'cell_phone',
        'compensation_fund_id',
        'birth_date',
        'degree',
        'direction',
        'address',
        'email',
        'eps_id',
        'first_name',
        'first_surname',
        'second_name',
        'second_surname',
        'gener',
        'identifier',
        'image',
        'type_document_id',
        'marital_status',
        'number_of_children',
        'pants_size',
        'pension_fund_id',
        'phone',
        'place_of_birth',
        'severance_fund_id',
        'shirt_size',
        'title',
        'personId',
        'persistedFaceId',
        'arl_id',
        "company_id",
        'status',
        'company_worked_id',
        'visa',
        'passport_number',
        'folder_id'
    ];

    public function scopeAlias($q, $alias)
    {
        return $q->from($q->getQuery()->from . " as " . $alias);
    }

    public function scopeLoans($q, $inicio, $fin)
    {
        return $q->where('state',"Pendiente")
                 ->whereBetween('date', [$inicio, $fin]);
    }

    /* public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['first_surname'];
    }*/

    public function scopeFullName($q)
    {
        return $q->select('*', DB::raw("CONCAT_WS(' ', first_name, second_name, first_surname, second_surname) as full_names"));
    }

    public function scopeCompleteName($q)
    {
        return $q->selectRaw("CONCAT_WS(' ', first_name, second_name, first_surname, second_surname) as complete_name");
    }

    public function contractultimate()
    {
        return $this->hasOne(WorkContract::class)->with('position.dependency', 'work_contract_type')->orderBy('id', 'DESC');
    }

    public function work_contract()
    {
        return $this->hasOne(WorkContract::class)->with('position', 'company');
    }

    public function work_contract_with_turn()
    {
        return $this->hasMany(WorkContract::class)->with('rotatingTurnWithDiaries');
    }

    public function work_contracts()
    {
        return $this->hasMany(WorkContract::class)->with('position', 'company');
    }
    public function loans_list()
    {
        return $this->hasMany(Loan::class,'person_id', 'id')->with('fees');
    }

    public function liquidado()
    {
        return $this->hasOne(WorkContract::class);
        //->with('cargo.dependencia.centroCosto', 'tipo_contrato')->where('liquidado', 1);
    }

    public function payroll_factors()
    {
        return $this->hasMany(PayrollFactor::class);
    }

    /**
     * una persona tiene muchas llegadas tardes
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lateArrivals()
    {
        return $this->hasMany(LateArrival::class);
    }

    /**
     * Un funcionario puede tener varios diarios fijos (dias de un turno fijo) (1,2,3,4,5 ó 6 a la semana)
     *
     * @return void
     */
    public function diariosTurnoFijo()
    {
        return $this->hasMany(DiarioTurnoFijo::class);
    }

    public function diariosTurnoRotativo()
    {
        return $this->hasMany(DiarioTurnoRotativo::class, 'person_id', 'id');
    }
    public function diariosTurnoRotativoAyer()
    {
        return $this->hasMany(DiarioTurnoRotativo::class);
    }
    public function diariosTurnoRotativoHoy()
    {
        return $this->hasMany(DiarioTurnoRotativo::class);
    }

    public function turnoFijo()
    {
        return $this->belongsTo(FixedTurn::class);
    }

    public function horariosTurnoRotativo()
    {
        return $this->hasMany(RotatingTurnHour::class)->with('turnoRotativo');
    }
    public function documentType()
    {
        return $this->belongsTo(DocumentTypes::class, 'type_document_id');
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function companyWorked()
    {
        return $this->belongsTo(Company::class, 'company_worked_id');
    }

    public function severance_fund()
    {
        return $this->belongsTo(SeveranceFund::class, 'id');
    }

    public function liquidation()
    {
        return $this->hasOne(Liquidation::class);
    }

    public function responsableNomina()
    {
        return $this->belongsTo(PayrollManager::class, 'identifier', 'manager');
    }

    /**
     * último registro del pago de la nomina individual
     */
    public function personPayrollPayment()
    {
        return $this->hasOne(PersonPayrollPayment::class, 'person_id', 'id')->latest();
    }

    /***
     * Todos los pagos de nómina, sirve para ver la variación del salario en el tiempo
     */
    public function personPayrollPayments()
    {
        return $this->hasMany(PersonPayrollPayment::class, 'person_id', 'id');
    }

    public function bonusPerson()
    {
        return $this->hasMany(BonusPerson::class);
    }

    public function preliquidated_logs()
    {
        return $this->hasMany(PreliquidatedLog::class, 'person_id', 'id');
    }

    /**Trae el último registro cuyo estatus sea PreLiquidado */
    public function onePreliquidatedLog()
    {
        return $this->hasOne(PreliquidatedLog::class, 'person_id', 'id')
            ->withDefault(function ($person, $prelg) {
                $prelg->status = 'PreLiquidado';
            });
    }

    public function scopeName($q)
    {
        return $q->select('*', DB::raw('CONCAT_WS(" ", first_name, first_surname) as person'));
    }

    public function scopeOnlyName($q)
    {
        return $q->select('image', DB::raw('CONCAT_WS(" ", first_name, first_surname) as person'));
    }
}
