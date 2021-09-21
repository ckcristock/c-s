<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
	use HasFactory;
	protected $fillable = [
		'type',
		'name',
		'address',
		'rate',
		'phone'
	];
	public function city()
	{
		return $this->belongsTo(city::class);
	}
	public function travelExpenses()
	{

		return $this->belongsToMany(TravelExpense::class);
	}
}
