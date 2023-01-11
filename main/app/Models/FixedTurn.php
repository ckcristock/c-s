<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedTurn extends Model
{
	use HasFactory;
	protected $fillable = [
		"name",
		"extra_hours",
		"entry_tolerance",
		"leave_tolerance",
        "color",
        "state",
	];
	public function horariosTurnoFijo()
	{
		return $this->hasMany(FixedTurnHour::class);
	}

	public function diariosTurnoFijo()
	{
		return $this->hasMany(DiarioTurnoFijo::class);
	}
}
