<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalProcess extends Model
{
    protected $fillable = [
        "name",
        "unit_cost",
        "unit_id"
    ];
    public function unit(){
        return $this->BelongsTo(Unit::class);
    }
}
