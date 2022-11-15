<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkContractFinishConditions extends Model
{
    use HasFactory;

    protected $fillable = [
        'renewed','contract_id','work_contract_type_id','contract_term_id','position_id','company_id','liquidated','person_id','salary','turn_type','fixed_turn_id','rotating_turn_id','date_of_admission','date_end','old_date_end','created_at','updated_at'
    ];
}
