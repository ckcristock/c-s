<?php

namespace App\Models;

use Doctrine\Inflector\Rules\Word;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'status'
    ];

    protected $table = 'contract_terms';

    public function workContractTypes ()
    {
        return $this->belongsToMany(WorkContractType::class)
             ->withTimestamps();
    }

}
