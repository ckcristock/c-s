<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CausalNoConforme extends Model
{
    use HasFactory;
    protected $table = 'Causal_No_Conforme';
    protected $primaryKey = 'Id_Causal_No_Conforme';
    protected $fillable = [
        'Codigo',
        'Nombre',
        'Tratamiento',
    ];
}
