<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPlanBalance extends Model
{
	use HasFactory;

    protected $fillable = [
        'balance',
        'account_plan_id'
    ];

	public function accountPlan()
	{
		return $this->belongsTo(AccountPlan::class);
	}
}
