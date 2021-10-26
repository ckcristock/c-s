<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    use HasFactory;

    public function scopePeriodo($query, Person $funcionario, $fechaInicio, $fechaFin)
    {
        return $query->where('person_id', '=', $funcionario->id)
        ->whereBetween('created_at', [$fechaInicio, $fechaFin])->with('deduccion')->get();
    }
}
