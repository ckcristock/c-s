<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    use HasFactory;
    protected $fillable =[
        'person_id',
        'date',
        'comment',
        'task_id',
    ];

    public function autor() 
    {
        return $this->hasOne(Person::class, 'id', 'person_id');
    }
}
