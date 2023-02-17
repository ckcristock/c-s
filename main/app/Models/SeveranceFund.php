<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeveranceFund extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'nit',
        'status'
    ];

    public function personAfiliates()
    {
        return $this->hasMany(Person::class, 'severance_fund_id', 'id');
    }
}
