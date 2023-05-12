<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'icon',
        'title',
        'person_id',
        'description',
        'status'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class)->fullName();
    }
}
