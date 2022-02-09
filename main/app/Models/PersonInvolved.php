<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonInvolved extends Model
{
    use HasFactory;
    protected $fillable = ['observation', 'disciplinary_process_id', 'file', 'user_id', 'person_id', 'state'];

    public function user()
    {
        return $this->belongsTo(User::class)->with([
            'person' => function($q){
                $q->select('id', 'first_name', 'second_name', 'first_surname', 'second_surname');
            }
        ]);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function memorandumInvolved()
    {
        return $this->hasMany(MemorandumInvolved::class)->with('memorandum');
    }

}
