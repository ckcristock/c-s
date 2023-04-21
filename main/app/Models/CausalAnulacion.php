<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CausalAnulacion extends Model
{
    use HasFactory;
    protected $table = 'Causal_Anulacion';
    protected $primaryKey = 'Id_Causal_Anulacion';
    protected $fillable = [
        'Nombre'
    ];
}
