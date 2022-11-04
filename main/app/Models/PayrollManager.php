<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollManager extends Model
{
    use SoftDeletes;

    use HasFactory;

    protected $fillable = [
        'area',
        'manager'
    ];

    public function responsable()
    {
        return $this->hasOne(Person::class, 'identifier', 'manager');
    }
}
