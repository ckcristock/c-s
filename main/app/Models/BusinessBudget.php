<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessBudget extends Model
{
    use HasFactory;
    protected $fillable = ['budget_id', 'business_id'];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }
}
