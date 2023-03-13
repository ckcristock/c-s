<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormaPago extends Model
{
    use HasFactory;
    protected $table = 'Forma_Pago';
    protected $primaryKey= 'Id_Forma_Pago';
    protected $fillable = [
        'Nombre',
    ];
}
