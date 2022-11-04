<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxiCity extends Model
{
	use HasFactory;
	protected $fillable = [
		'type',
		'taxi_id',
		'city_id', 
		'value'
	];
	public function city()
	{
		return $this->belongsTo(Municipality::class);
	}
	public function taxi()
	{
		return $this->belongsTo(Taxi::class);
	}
}
