<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelExpenseTaxiCity extends Model
{
	use HasFactory;
	protected $guarded = ['id'];

	public function taxiCity()
	{
		return $this->belongsTo(TaxiCity::class);
	}
}
