<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayConfigurationCompany extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'affect_transportation_assistance' => 'boolean',
        'vacations_31_pay' => 'boolean'
    ];

    protected $columns = ['id', 'disability_percentage_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function percentage()
    {
        return $this->belongsTo(DisabilityPercentage::class,'disability_percentage_id','id');
    }

    public function scopeExclude($query, $value = [])
    {
        return $query->select(array_diff($this->columns, (array) $value));
    }
}
