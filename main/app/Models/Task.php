<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable =[
        'id_realizador',
        'type_id',
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

    public function types()
    {
        return $this->hasOne(TaskType::class, 'id', 'type_id');
    }

    public function timeline()
    {
        return $this->hasMany(TaskTimeline::class)->with('person')->orderByDesc('created_at');
    }
}
