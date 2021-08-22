<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiarioTurnoRotativo extends Model
{
    protected $table = 'diario_turno_rotativo';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];


    public function turnoRotativo()
    {
        return $this->belongsTo(RotatingTurn::class, 'turno_rotativo_id');
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }
}
