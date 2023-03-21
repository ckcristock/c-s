<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryDataCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'namespace',
        'data_name',
        'date_end',
        'value',
        'person_id',
    ];

    public function Person(){
        return $this->belongsTo(Person::class)->completeName();
    }

}
