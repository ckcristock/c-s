<?php

namespace App\Models;

use Doctrine\Inflector\Rules\Word;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status'
    ];

    public function workContractTypes ()
    {
        $this->belongsToMany(WorkContractType::class)
             ->withPivot('id', 'name', 'status')
             ->withTimestamps();
    }

}
