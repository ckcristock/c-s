<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiarioTurnoFijo extends Model
{
    protected $table = 'diario_turno_fijo';
    protected $guarded = ['id'];

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }

    public function turnoFijo()
    {
        return $this->belongsTo(FixedTurn::class);
    }
}
