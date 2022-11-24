<?php

namespace App\Models;

use App\Http\Controllers\LateArrivalController;
use App\Services\RotatingHourService;
use Illuminate\Database\Eloquent\Model;

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

    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['first_surname'];
    }

    public function contractultimate()
    {
        return $this->hasOne(WorkContract::class)->with('position.dependency', 'work_contract_type')->orderBy('id','DESC');

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
        return $this->hasMany(RotatingTurnHour::class);
    }
    public function documentType()
    {
        return $this->belongsTo(DocumentTypes::class,'type_document_id');
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function companyWorked()
    {
        return $this->belongsTo(Company::class,'company_worked_id');
    }

    public function liquidation()
    {
        return $this->hasOne(Liquidation::class);
    }

    public function responsableNomina ()
    {
        return $this->belongsTo(PayrollManager::class,'identifier', 'manager');
    }

}
