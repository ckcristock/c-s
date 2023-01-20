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
		//'rate',
		'phone',
		'landline',
		'city_id',
		'simple_rate',
		'double_rate',
		'breakfast',
		//'accommodation_id',
	];

	public function city()
	{
        //este cambio se debe a que se usa Munucipality para las ciudades
        //Dejo la misma relación de city, pero a la tabla municipality para evitar
        //que se rompa en otra parte __dy
		//return $this->belongsTo(City::class);
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
