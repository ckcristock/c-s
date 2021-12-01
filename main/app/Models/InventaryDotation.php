<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventaryDotation extends Model
{
    use HasFactory;


    public function dotacionProducto(){
        return $this->hasMany(DotationProduct::class);
    }
}
