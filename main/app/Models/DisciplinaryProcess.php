<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DisciplinaryProcess extends Model
{
    use HasFactory;
    protected $fillable  = [
        'person_id',
        'code',
        'process_description',
        'date_of_admission',
        'date_end',
        'status',
        'file',
        'approve_user_id',
        'close_description',
        'fileType'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function personInvolved()
    {
        return $this->hasMany(PersonInvolved::class)->with([
            'person' => function($q){
                $q->select('id', 'first_name', 'second_name', 'first_surname', 'second_surname');
            }
        ]);
    }
}
