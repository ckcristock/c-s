<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskTimeline extends Model
{
    use HasFactory;
    protected $fillable = [
        'icon',
        'title',
        'description',
        'task_id',
        'person_id',
    ];

    public function person()
    {
        return $this->hasOne(Person::class, 'id', 'person_id');
    }
}
