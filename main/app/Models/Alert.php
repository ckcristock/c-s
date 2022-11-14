<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;
    protected $fillable = [
        'person_id', //emisor
        'title',
        'modal',
        'user_id', //receptor
        'type',
        'icon',
        'description',
        'url',
        'destination_id',
        'read_boolean',
        'created_at',
    ];

    public function transmitter()
    {
        return $this->hasOne(Person::class, 'id', 'person_id');
    }
}
