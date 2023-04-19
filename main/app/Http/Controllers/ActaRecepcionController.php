<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Services\consulta;

class ActaRecepcionController extends Controller
{
    use ApiResponser;

    public function listarPendientes()
    {
        $query = 'SELECT ARC.Id_Acta_Recepcion, ARC.Codigo, ARC.Fecha_Creacion, F.image as Imagen, B.Nombre as Bodega, OCN.Codigo as Codigo_Compra_N,  OCN.Id_Bodega_Nuevo,
        (CASE
                WHEN ARC.Tipo = "Nacional" THEN ARC.Id_Orden_Compra_Nacional
                ELSE ARC.Id_Orden_Compra_Internacional
        END) AS Id_Orden_Compra,
        ( SELECT GROUP_CONCAT(Factura) FROM Factura_Acta_Recepcion WHERE Id_Acta_Recepcion = ARC.Id_Acta_Recepcion ) as Facturas,
        ARC.Tipo, ARC.Estado
        FROM Acta_Recepcion ARC
        LEFT JOIN people F
        ON F.id = ARC.Identificacion_Funcionario
        LEFT JOIN Orden_Compra_Nacional OCN
        ON OCN.Id_Orden_Compra_Nacional = ARC.Id_Orden_Compra_Nacional
        LEFT JOIN Bodega_Nuevo B
        ON B.Id_Bodega_Nuevo = ARC.Id_Bodega_Nuevo
        WHERE ARC.Estado = "Pendiente"
        HAVING OCN.Id_Bodega_Nuevo
        ORDER BY ARC.Fecha_Creacion DESC, ARC.Codigo DESC';

        $oCon= new consulta();
        $oCon->setTipo('Multiple');
        $oCon->setQuery($query);
        $actarecepcion= $oCon->getData();
        unset($oCon);



        echo json_encode($actarecepcion);
    }
}
