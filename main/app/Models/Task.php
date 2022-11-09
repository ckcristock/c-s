<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable =[
        'id_realizador',
        'tipo',
        'titulo',
        'descripcion',
        'fecha',
        'adjunto',
        'link',
        'id_asignador',
        'hora',
        'estado',
    ];

    public function asignador() 
    {
        return $this->hasOne(Person::class, 'id', 'id_asignador');
    }

    public function realizador() 
    {
        return $this->hasOne(Person::class, 'id', 'id_realizador');
    }

    public function adjuntos()
    {
        return $this->hasMany(TaskFile::class);
    }

    public function comment()
    {
        return $this->hasMany(TaskComment::class)->with('autor');
    }
}
