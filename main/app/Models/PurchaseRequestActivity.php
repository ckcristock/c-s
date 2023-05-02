<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_purchase_request',
        'user_id',
        'date',
        'details',
        'status'
    ];

    public function person()
    {
        return $this-> belongsTo(Person::class, 'user_id')->fullName();
    }
}
