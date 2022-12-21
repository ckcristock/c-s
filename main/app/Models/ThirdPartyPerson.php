<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdPartyPerson extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'n_document',
        'landline',
        'cell_phone',
        'email',
        'position',
        'observation',
        'third_party_id'
    ];

    public function thirdParty()
    {
        return $this->belongsTo(ThirdParty::class)->name();
    }

}
