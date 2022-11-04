<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ContractTermWorkContractType extends Pivot
{
    protected $timestamp = true;

    protected $fillable = [
        'work_contract_type_id',
        'contract_term_id'
    ];

}
