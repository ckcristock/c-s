<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remision extends Model
{
    use HasFactory;

    protected $table = 'Remision';
    protected $primaryKey = 'Id_Remision';
    protected $fillable = [
        'Id_Contrato',
        'Fecha',
        'Tipo',
        'Identificacion_Funcionario',
        'Observaciones',
        'Codigo',
        'Tipo_Origen',
        'Id_Origen',
        'Id_Orden_Pedido',
        'Nombre_Origen',
        'Tipo_Destino',
        'Id_Destino',
        'Nombre_Destino',
        'Tipo_Lista',
        'Id_Lista',
        'Estado',
        'Estado_Alistamiento',
        'Prioridad',
        'Id_Factura',
        'Peso_Remision',
        'Codigo_Qr',
        'Costo_Remision',
        'Tipo_Bodega',
        'Fecha_Anulacion',
        'Funcionario_Anula',
        'Fase_1',
        'Fase_2',
        'Guia',
        'Empresa_Envio',
        'Subtotal_Remision',
        'Descuento_Remision',
        'Impuesto_Remision',
        'Orden_Compra',
        'Inicio_Fase1',
        'Fin_Fase1',
        'Inicio_Fase2',
        'Fin_Fase2',
        'Entrega_Pendientes',
        'Observacion_Anulacion',
        'Id_Categoria',
        'FIni_Rotativo',
        'FFin_Rotativo',
        'Eps_Rotaivo',
        'Id_Subcategoria',
        'Id_Categoria_Nueva',
        'Id_Grupo_Estiba',
        'Meses',
        'Id_Factura_Venta',
    ];
}
