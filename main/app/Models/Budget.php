<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function customer (){
        return $this->belongsTo( ThirdParty::class,'customer_id','id' );
    }
    public function user (){
        return $this->belongsTo( User::class );
    }
    public function destiny (){
        return $this->belongsTo( Municipality::class, 'destinity_id', 'id' );
    }
    public function items (){
        return $this->hasMany( BudgetItem::class );
    }
    public function indirectCosts (){
        return $this->hasMany( BudgetIndirectCost::class );
    }

}
