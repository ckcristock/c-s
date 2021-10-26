<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPlanBalance extends Model
{
	use HasFactory;
	public function accountPlan()
	{
		return $this->belongsTo(AccountPlan::class);
	}
}
