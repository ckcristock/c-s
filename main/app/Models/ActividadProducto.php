<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadProducto extends Model
{
    use HasFactory;
    protected $table = 'actividad_producto';
    protected $primaryKey = 'Id_Actividad_Producto';
    protected $fillable =
    [
        'Id_Actividad_Producto','Id_Producto','Person_Id','Detalles','Fecha'
    ];

    public function scopeAlias($q, $alias){
        return $q->from($q->getQuery()->from." as ".$alias);
    }

    public function producto()
    {
        return $this->belongsTo(Product::class, "Id_Producto");
    }

    public function funcionario()
    {
        return $this->belongsTo(Person::class, "Person_Id")->onlyName();
    }
}
