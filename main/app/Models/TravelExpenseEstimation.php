<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelExpenseEstimation extends Model
{
    use HasFactory;
    protected $fillable = ['description', 'unit', 'amount', 'unit_value', 'total_value', 'formula_amount', 'formula_total_value'];

    public function travelExpenseEstimationValues()
    {
        return $this->hasOne(TravelExpenseEstimationValues::class);
    }

}
