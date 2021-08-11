<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkContract extends Model
{

    use HasFactory;
    protected $fillable = [
        'date_of_admission',
        'date_end',
        'position_id',
        'salary',
        'turn_type',
        'work_contract_type_id',
       
    ];
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function work_contract_type()
    {
        return $this->belongsTo(workContractType::class);
    }
}
