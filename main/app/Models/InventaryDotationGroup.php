<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventaryDotationGroup extends Model
{
    //!Parece que no se usa --> no existe tabla
    use HasFactory;

    protected $fillable = ['name'];
}
