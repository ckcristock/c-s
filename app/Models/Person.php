<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $guarded = [''];

    protected $fillable = [
        'blood_type',
        'cell_phone',
        'compensation_fund_id',
        'date_of_birth',
        'degree',
        'direction',
        'email',
        'eps_id',
        'first_name',
        'first_surname',
        'gener',
        'identifier',
        'image',
        'marital_status',
        'number_of_children',
        'pants_size',
        'pension_fund_id',
        'phone',
        'place_of_birth',
        'severance_fund_id',
        'shirt_size',
        'title',
    ];
    
    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['first_surname'];
    }

    public function contractultimate()
    {
        return $this->hasOne(WorkContract::class)
        ->with(
        'work_contract_type')
        ->orderBy('id', 'Desc');

        //->with('cargo.dependencia.centroCosto', 'tipo_contrato');
    }
    public function liquidado()
    {
        return $this->hasOne(WorkContract::class);
        //->with('cargo.dependencia.centroCosto', 'tipo_contrato')->where('liquidado', 1);
    }

}
