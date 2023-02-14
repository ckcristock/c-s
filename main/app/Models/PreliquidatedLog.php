<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreliquidatedLog extends Model
{
    use HasFactory;

    protected $fillable = [
        "person_id",
        "person_identifier",
        "full_name",
        "liquidated_at",
        "person_work_contract_id",
        "reponsible_id",
        "responsible_identifier",
        "status",
    ];

    public function person ()
    {
        return $this->belongsTo(Person::class, 'id', 'person_id');
    }

    public function workContract ()
    {
        return $this->belongsTo(WorkContract::class, 'person_id', 'person_id');
    }

    public function user ()
    {
        return $this->belongsTo(User::class, 'person_id', 'person_id');
    }
}
