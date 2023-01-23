<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'department_id',
        'dane_code',
        'dian_code',
        'municipalities_id',
        'percentage_product',
        'percentage_service',
        'abbreviation',
    ];


    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
