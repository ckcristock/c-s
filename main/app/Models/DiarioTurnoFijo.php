<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiarioTurnoFijo extends Model
{
    protected $table = 'fixed_turn_diaries';

    protected $guarded = ['id'];

    protected $fillable = [
        'person_id',
        'date',
        'fixed_turn_id',
        'entry_time_one',
        'leave_time_one',
        'entry_time_two',
        'leave_time_two',
        'img_one',
        'img_two',
        'img_three',
        'img_four',
        'latitud',
        'longitud',
        'latitud_dos',
        'longitud_dos',
        'latitud_tres',
        'longitud_tres',
        'latitud_cuatro',
        'longitud_cuatro',
        'created_at',
        'updated_at',
        'temp_one',
        'temp_two',
        'temp_three',
        'temp_four',
   ];

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }

    public function turnoFijo()
    {
        return $this->belongsTo(FixedTurn::class);
    }
}
