<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrettyCash extends Model
{
	use HasFactory;
	protected $table = 'pretty_cash';
	protected $fillable = [
		'person_id',
		'account_plan_id',
		'initial_balance',
		'description',
		'user_id',
        'status'
	];

	function accountPlan()
	{
		return $this->belongsTo(PlanCuentas::class, 'account_plan_id', 'Id_Plan_Cuentas');
	}
	function person()
	{
		return $this->belongsTo(Person::class);
	}
	function user()
	{
		return $this->belongsTo(User::class);
	}
}
