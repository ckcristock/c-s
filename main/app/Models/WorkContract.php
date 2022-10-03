<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkContract extends Model
{

    use HasFactory;
    protected $fillable = [
        'position_id',
        'date_end',
        'old_date_end',
        'salary',
        'turn_type',
        'work_contract_type_id',
        'rotating_turn_id',
        'company_id',
        'fixed_turn_id',
        'person_id',
        'date_of_admission'
    ];
    public function position()
    {
        return $this->belongsTo(Position::class)->with('dependency');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function people(){
        return $this->belongsTo(People::class);
    }

    public function work_contract_type()
    {
        return $this->belongsTo(WorkContractType::class);
    }

    /**
     * El contrato pertenece a un funcionario
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Get the user that owns the WorkContract
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fixedTurn()
    {
        return $this->belongsTo(FixedTurn::class);
    }

    public function bonifications()
    {
        return $this->hasMany(Bonifications::class);
    }

}
