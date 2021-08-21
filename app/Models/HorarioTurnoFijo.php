<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioTurnoFijo extends Model
{

    protected $guarded = ['id'];
    protected $table = 'horario_turno_fijo';
    protected $hidden = ['created_at', 'updated_at'];

    public function turnoFijo()
    {
        return $this->belongsTo(TurnoFijo::class, 'turno_fijo_id');
    }
}
