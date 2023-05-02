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
		'city_id',
		'person_contact',
		'landline',
		'address',
		'phone',
		'simple_rate',
		'double_rate',
		'breakfast',
		'accommodation_id',
	];

	public function city()
	{
		return $this->belongsTo(Municipality::class);
	}
	public function travelExpenses()
	{
		return $this->belongsToMany(TravelExpense::class);
	}

    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class)->withPivot('price');
    }

    public function alojamiento()
    {
        return $this->belongsToMany(Accommodation::class);
    }

}
