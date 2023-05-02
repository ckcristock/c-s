<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelExpense extends Model
{
	use HasFactory;
	protected $guarded = ['id'];
    protected $fillale = [
        'person_id',
        'user_id',
        'approve_user_id',
        'origin_id',
        'destinity_id',
        'travel_type',
        'departure_date',
        'arrival_date',
        'n_nights',
        'baggage_usd',
        'baggage_cop',
        'total_hotels_usd',
        'total_hotels_cop',
        'total_transports_cop',
        'total_taxis_usd',
        'total_taxis_cop',
        'total_feedings_usd',
        'total_feedings_cop',
        'total_laundry_cop',
        'total_laundry_usd',
        'other_expenses_usd',
        'other_expenses_cop',
        'total_usd',
        'total_cop',
        'total',
        'work_order_id',
        'observation',
        'state',
    ];

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

    public function work_order ()
    {
        return $this->hasOne(WorkOrder::class, 'id', 'work_order_id')->select('id', 'id as value', 'code as text');
    }
}
