<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelExpense extends Model
{
	use HasFactory;
	protected $guarded = ['id'];

	public function destiny()
	{
		return $this->belongsTo(Municipality::class, 'destinity_id');
	}
	public function origin()
	{
		return $this->belongsTo(Municipality::class, 'origin_id');
	}
	public function user()
	{
		return $this->belongsTo(User::class)->with('person');
	}
	public function person()
	{
		return $this->belongsTo(Person::class)->with('contractultimate');
	}
	public function transports()
	{
		return $this->hasMany(TravelExpenseTransport::class);
	}
	public function feedings()
	{
		return $this->hasMany(TravelExpenseFeeding::class);
	}
	public function hotels()
	{
		return $this->belongsToMany(Hotel::class, 'travel_expense_hotels')
        ->with('accommodations')
        ->withPivot('who_cancels', 'n_night', 'breakfast', 'total', 'breakfast', 'rate', 'accommodation');
        /* ->with(['accommodations'=>function ($query){
            $query->where('accommodation_hotel.id', '=', 'travel_expense_hotels.pivot.accommodation');
        }]) */
	}
	public function expenseTaxiCities()
	{
		return $this->hasMany(TravelExpenseTaxiCity::class);
	}

 /*    public function accommodation ()
    {
        return $this->hasOne(Accommodation::class, 'id', 'accommodation');
    } */

    public function te_hotel ()
    {
        return $this->hasMany(TravelExpenseHotel::class)->with('accommodation');
    }
}
