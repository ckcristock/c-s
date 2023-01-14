<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BusinessHistory extends Model
{
    use HasFactory;

    protected $name = 'business_histories';
    protected $fillable = [
        'business_id',
        'icon',
        'title',
        'person_id',
        'description'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class)->select([DB::raw("CONCAT_WS(' ', first_name, second_name, first_surname, second_surname) as full_name"),'image','id']);
    }
}
