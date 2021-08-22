<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedTurn extends Model
{
    use HasFactory;
    public function horariosTurnoFijo()
    {
        return $this->hasMany(HorarioTurnoFijo::class);
    }

    public function diariosTurnoFijo()
    {
        return $this->hasMany(DiarioTurnoFijo::class);
    }
}
