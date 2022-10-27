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
        'codigo_dane',
        'municipalities_id',
    ];


    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
