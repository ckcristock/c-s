<?php

namespace App\Models;

use Doctrine\Inflector\Rules\Word;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractTerm extends Model
{
    use HasFactory;
    protected $table = 'contract_terms';
    protected $fillable = [
        'id',
        'name',
        'status',
        'conclude',
        'modified',
        'description',
    ];

    public function workContractTypes ()
    {
        return $this->belongsToMany(WorkContractType::class)
             ->withTimestamps();
    }

}
