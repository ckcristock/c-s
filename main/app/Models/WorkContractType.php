<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkContractType extends Model
{
    use HasFactory;

    protected $fillable = ['id','name','description', 'conclude', 'status','dian_code'];

    protected $table = 'work_contract_types';

    public function contractTerms()
    {
        return $this->belongsToMany(ContractTerm::class)->withTimestamps();
    }
}
