<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoActaRecepcion extends Model
{
    use HasFactory;

    protected $table = 'Producto_Acta_Recepcion';
    protected $primaryKey = 'Id_Producto_Acta_Recepcion';
    protected $fillable = [
        'Cantidad',
        'Precio',
        'Impuesto',
        'Subtotal',
        'Lote',
        'Fecha_Vencimiento',
        'Factura',
        'Id_Producto',
        'Id_Producto_Orden_Compra',
        'Codigo_Compra',
        'Tipo_Compra',
        'Id_Acta_Recepcion',
        'Temperatura',
        'Cumple'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'Id_Producto', 'Id_Producto')->with('unit');
    }
    public function factura()
    {
        return $this->hasOne(FacturaActaRecepcion::class, 'Id_Factura_Acta_Recepcion', 'Factura');
    }

    public function unidad()
    {
        return $this->hasOne(FacturaActaRecepcion::class, 'Id_Factura_Acta_Recepcion', 'Factura');
    }


}
