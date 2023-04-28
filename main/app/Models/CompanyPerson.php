<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyPerson extends Model
{
    use HasFactory;
    protected $table = 'company_person';
    protected $fillable = [
        'person_id',
        'company_id'
    ];
}
