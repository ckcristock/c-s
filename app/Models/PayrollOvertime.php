<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollOvertime extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function scopePrefijo($query, $indice)
    {
        return $query->where('prefix',$indice)->first(['prefix'])['prefix'];
    }

    public static function enviarPorcentajes($indices = [])
    {   
        $porcentajes = [];
        foreach($indices as $indice) {
            $porcentajes[$indice] = self::prefijo($indice);
        }
        return $porcentajes;

    }
}
