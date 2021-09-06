<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeService extends Model
{
	//

	protected $fillable=[
		'name'
	];
	public function formalities()
	{
		return $this->belongsToMany(TypeService::class);
	}
}
