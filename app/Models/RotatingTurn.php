<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RotatingTurn extends Model
{
	use HasFactory;
	protected $fillable = [
		"name",
		"entry_tolerance",
		"leave_tolerance",
		"extra_hours",
		"entry_time",
		"leave_time",
		"launch",
		"launch_time",
		"launch_time_two",
		"breack",
		"breack_time",
		"breack_time_two",
		"sunday_id",
		"saturday_id",
	];

	public function sunday()
	{
		return $this->belongsTo(RotatingTurn::class , 'sunday_id','id');
	}
	public function saturday()
	{
		return $this->belongsTo(RotatingTurn::class , 'saturday_id','id');
	}
}
