<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Services\consulta;
use App\Http\Services\QueryBaseDatos;
use App\Http\Services\PaginacionData;
use App\Models\ActaRecepcion;
use App\Models\CausalAnulacion;
use Exception;

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

        $oCon = new consulta();
        $oCon->setTipo('Multiple');
        $oCon->setQuery($query);
        $actarecepcion = $oCon->getData();
        unset($oCon);



        echo json_encode($actarecepcion);
    }

    public function listarAnuladas()
    {
        $pag = (isset($_REQUEST['pag']) ? $_REQUEST['pag'] : '');
        $tam = (isset($_REQUEST['tam']) ? $_REQUEST['tam'] : '');
        $condicion = '';

        if (isset($_REQUEST['codigo']) && $_REQUEST['codigo']) {
            $condicion .= " AND AR.Codigo LIKE '%" . $_REQUEST['codigo'] . "%'";
        }
        $query = 'SELECT
            AR.*,
            OCN.Codigo AS Codigo_Orden,(SELECT IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname)) FROM third_parties WHERE id=AR.Id_Proveedor) as Proveedor
        FROM Acta_Recepcion AR
        INNER JOIN Orden_Compra_Nacional OCN ON AR.Id_Orden_Compra_Nacional = OCN.Id_Orden_Compra_Nacional
        WHERE AR.Estado="Anulada" ' . $condicion . ' Order By AR.Id_Acta_Recepcion DESC  ';

        $query_count = '
        SELECT
            COUNT(AR.Id_Acta_Recepcion) AS Total
            FROM Acta_Recepcion AR
            INNER JOIN Orden_Compra_Nacional OCN ON AR.Id_Orden_Compra_Nacional = OCN.Id_Orden_Compra_Nacional
            WHERE AR.Estado="Anulada" ' . $condicion;

        $paginationData = new PaginacionData($tam, $query_count, $pag);
        $queryObj = new QueryBaseDatos($query);
        $actas_realizadas = $queryObj->Consultar('Multiple', true, $paginationData);

        echo json_encode($actas_realizadas);
    }

    public function indexCausalAnulacion()
    {
        return $this->success(CausalAnulacion::orderBy('Nombre')->get());
    }

    public function listarActas()
    {
        $condicion = '';
        $condicion2 = '';
        $buscarActaRecepcionRemision = true; //validar si no hay campos de busqueda únicos de Acta Recepcion
        //el estado de la acta recepcion (aprobada-acomodada)
        $id_funcionario = isset($_REQUEST['id_funcionario']) ? $_REQUEST['id_funcionario']  : '';
        $tipo = isset($_REQUEST['tipo']) ? $_REQUEST['tipo']  : '';
        #si existe tipo = general no tomar en cuenta condicion de bodegas asociadas al funcionario
        $enbodega = '';
        if ($tipo != "General") {
            //buscar bodegas asociadas al  funcionario
            $query = 'SELECT GROUP_CONCAT(Id_Bodega_Nuevo) AS Id_Bodega_Nuevo FROM Funcionario_Bodega_Nuevo WHERE Identificacion_Funcionario = ' . $id_funcionario;
            $oCon = new consulta();
            $oCon->setQuery($query);
            $bodegas_funcionario = $oCon->getData();
            $bodegas_funcionario = $bodegas_funcionario['Id_Bodega_Nuevo'];
            unset($oCon);
            $enbodega = ' AND  ( AR.Id_Bodega_Nuevo IN(' . $bodegas_funcionario . ') OR AR.Id_Bodega_Nuevo = 0) ';
        }
        if (isset($_REQUEST['estado']) && $_REQUEST['estado'] != "") {
            $estado = $_REQUEST['estado'];

            if ($estado == 'Acomodada') {
                $estadoCond = ' (AR.Estado ="' . $estado . '" OR AR.Fecha_Creacion <"2020-07-22") ';
            } else if ($estado == 'Aprobada') {
                $estadoCond = ' AR.Estado ="' . $estado . '" AND AR.Fecha_Creacion > "2020-07-22" ';
            }
        }
        if (isset($_REQUEST['cod']) && $_REQUEST['cod'] != "") {
            $condicion .= " AND AR.Codigo LIKE '%$_REQUEST[cod]%'";
        }
        if (isset($_REQUEST['compra']) && $_REQUEST['compra'] != "") {
            $condicion .= " AND (AR.Codigo_Compra_N LIKE '%$_REQUEST[compra]%')";
            $buscarActaRecepcionRemision = false;
        }
        if (isset($_REQUEST['proveedor']) && $_REQUEST['proveedor'] != "") {
            $condicion .= " AND AR.proveedor LIKE '%$_REQUEST[proveedor]%'";
            $buscarActaRecepcionRemision = false;
        }
        if (isset($_REQUEST['fecha']) && $_REQUEST['fecha'] != "") {
            $fecha_inicio = trim(explode(' - ', $_REQUEST['fecha'])[0]);
            $fecha_fin = trim(explode(' - ', $_REQUEST['fecha'])[1]);
            $condicion .= " AND DATE_FORMAT(AR.Fecha_Creacion, '%Y-%m-%d') BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        }
        if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != "") {
            $fecha_inicio = trim(explode(' - ', $_REQUEST['fecha2'])[0]);
            $fecha_fin = trim(explode(' - ', $_REQUEST['fecha2'])[1]);
            $condicion .= " AND (( DATE(AR.Fecha_Compra_N) BETWEEN '$fecha_inicio' AND '$fecha_fin'))";
            $buscarActaRecepcionRemision = false;
        }
        if (isset($_REQUEST['fact']) && $_REQUEST['fact'] != "") {
            $condicion .= " AND AR.Facturas LIKE '%$_REQUEST[fact]%'";
            $buscarActaRecepcionRemision = false;
        }
        ####### PAGINACIÓN ########
        $tamPag = 10;
        $numReg = 50;
        $paginas = ceil($numReg / $tamPag);
        $limit = "";
        $paginaAct = "";

        if (!isset($_REQUEST['pag']) || $_REQUEST['pag'] == '') {
            $paginaAct = 1;
            $limit = 0;
        } else {
            $paginaAct = $_REQUEST['pag'];
            $limit = ($paginaAct - 1) * $tamPag;
        }
        /* $actarecepcion['numReg'] = $numReg; */
        /* if ($Actas_Recepcion_Remision) {
            # code...
            $actarecepcion['actarecepciones'] = array_merge($actarecepcion['actarecepciones'], $Actas_Recepcion_Remision, $Ajuste_Individual);
        } */
        $query = 'SELECT SQL_CALC_FOUND_ROWS AR.* FROM (
            #ACTAS DE RECEPCION
            SELECT
            ARC.Id_Acta_Recepcion AS Id_Acta, ARC.Codigo, ARC.Estado,
            ARC.Fecha_Creacion,ARC.Tipo_Acta,  F.image AS Imagen,
            B.Nombre as Bodega,  B.Id_Bodega_Nuevo AS Id_Bodega_Nuevo ,  OCN.Codigo as Codigo_Compra_N,  P.Nombre as Proveedor,
            NULL Codigo_Remision, OCN.created_at as Fecha_Compra_N,
            (
                CASE
                    WHEN ARC.Tipo = "Nacional" THEN ARC.Id_Orden_Compra_Nacional
                    ELSE ARC.Id_Orden_Compra_Internacional
                END
            ) AS Id_Orden_Compra,
            ( SELECT GROUP_CONCAT(Factura) FROM Factura_Acta_Recepcion WHERE Id_Acta_Recepcion = ARC.Id_Acta_Recepcion ) as Facturas,
                ARC.Tipo , "Acta_Recepcion" Tipo_Acomodar
            FROM Acta_Recepcion ARC
            LEFT JOIN people F
            ON F.id = ARC.Identificacion_Funcionario
            LEFT JOIN Orden_Compra_Nacional OCN
            ON OCN.Id_Orden_Compra_Nacional = ARC.Id_Orden_Compra_Nacional
            LEFT JOIN Bodega_Nuevo B
            ON B.Id_Bodega_Nuevo = ARC.Id_Bodega_Nuevo

            INNER JOIN Proveedor P
            ON P.Id_Proveedor = ARC.Id_Proveedor

            UNION ALL
            #ACTA DE RECEPCION REMISION
            SELECT ARC.Id_Acta_Recepcion_Remision  AS Id_Acta , ARC.Codigo,  ARC.Estado ,
            ARC.Fecha AS Fecha_Creacion, "INTERNA" as Tipo_Acta ,     F.image AS Imagen,
            NULL as Bodega, ARC.Id_Bodega_Nuevo ,  "INTERNA"  as Codigo_Compra_N, "INTERNA" Proveedor,
            R.Codigo as Codigo_Remision, "INTERNA" Fecha_Compra_N,
            NULL AS Id_Orden_Compra,
            NULL  AS Facturas,
            "INTERNA" as Tipo , "Acta_Recepcion_Remision" Tipo_Acomodar
            FROM Acta_Recepcion_Remision ARC
            INNER JOIN people F
            ON F.id = ARC.Identificacion_Funcionario
            INNER JOIN Remision R
            ON ARC.Id_Remision=R.Id_Remision
            WHERE  ARC.Id_Bodega_Nuevo IS NOT NULL


            UNION ALL
            #AJUSTE INDIVIDUAL
            SELECT AI.Id_Ajuste_Individual AS Id_Acta  , AI.Codigo, AI.Estado_Entrada_Bodega AS Estado,
            AI.Fecha AS "Fecha_Creacion", "INTERNA" AS Tipo_Acta , F.image AS Imagen,
            NULL as Bodega,  AI.Id_Origen_Destino AS Id_Bodega_Nuevo ,"INTERNA" Codigo_Compra_N,"INTERNA" Proveedor,
            "AJUSTE INDIVIDUAL" as Codigo_Remision, "INTERNA" Fecha_Compra_N ,
            NULL AS Id_Orden_Compra,
            "INTERNA" Facturas,
            "INTERNA" as Tipo , "Ajuste_Individual" Tipo_Acomodar
            FROM Ajuste_Individual AI
            INNER JOIN people F
            ON F.id = AI.Identificacion_Funcionario
            WHERE AI.Tipo="Entrada" AND AI.Estado!="Anulada" AND ( AI.Origen_Destino = "Bodega"  OR AI.Origen_Destino = "INTERNA"  )
            AND AI.Estado_Entrada_Bodega = "' . $estado . '" AND AI.Id_Origen_Destino IS NOT NULL

        UNION ALL
            SELECT NC.Id_Nota_Credito AS Id_Acta  , NC.Codigo, NC.Estado,
            NC.Fecha AS "Fecha_Creacion", "INTERNA" AS Tipo_Acta , F.image AS Imagen,
            NULL as Bodega, NC.Id_Bodega_Nuevo, "INTERNA" Codigo_Compra_N,"INTERNA" Proveedor,
            "AJUSTE INDIVIDUAL" as Codigo_Remision, "INTERNA" Fecha_Compra_N ,
            NULL AS Id_Orden_Compra,
            "INTERNA" Facturas,
            "INTERNA" as Tipo , "Nota_Credito" Tipo_Acomodar
            FROM Nota_Credito NC
            INNER JOIN people F
            ON F.id = NC.Identificacion_Funcionario
            WHERE NC.Estado = "' . $estado . '" AND NC.Id_Bodega_Nuevo IS NOT NULL

            # ACTA INTERNACIONAL
            UNION ALL
            SELECT PAI.Id_Nacionalizacion_Parcial AS Id_Acta  , PAI.Codigo, "Aprobada" AS Estado,
            PAI.Fecha_Registro AS "Fecha_Creacion", "INTERNA" AS Tipo_Acta , F.image AS Imagen,
            NULL as Bodega, ACI.Id_Bodega_Nuevo, "INTERNA" Codigo_Compra_N,"INTERNA" Proveedor,
            "AJUSTE INDIVIDUAL" as Codigo_Remision, "INTERNA" Fecha_Compra_N ,
            NULL AS Id_Orden_Compra,
            "INTERNA" Facturas,
            "INTERNA" as Tipo , "Nacionalizacion_Parcial" Tipo_Acomodar
            FROM Nacionalizacion_Parcial PAI
            INNER JOIN people F
            ON F.id = PAI.Identificacion_Funcionario
            INNER JOIN Acta_Recepcion_Internacional ACI ON ACI.Id_Acta_Recepcion_Internacional =  PAI.Id_Acta_Recepcion_Internacional
            WHERE PAI.Estado = "' . ($estado == "Aprobada" ? "Nacionalizado" : "Acomodada") . '" AND ACI.Id_Bodega_Nuevo IS NOT NULL

            ) AR


            WHERE  ' . $estadoCond . '
        AND (AR.Tipo_Acta = "Bodega"  OR AR.Tipo_Acta = "INTERNA"  )
       ' . $enbodega . '
    ' . $condicion . ' ORDER BY Fecha_Creacion DESC, Codigo DESC LIMIT ' . $limit . ',' . $tamPag;



        $oCon = new consulta();
        $oCon->setTipo('Multiple');
        $oCon->setQuery($query);
        $res = $oCon->getData();
        $actarecepcion['actarecepciones'] = $res;
        unset($oCon);



        $actarecepcion['numReg'] = $res;




        echo json_encode($actarecepcion);
    }

    public function detalleActa()
    {
        //return $this->success(ActaRecepcion::with('bodega', 'person', 'third', 'causal')->find(1));
        $id_acta = (isset($_REQUEST['id']) ? $_REQUEST['id'] : '');
        $tipo = (isset($_REQUEST['Tipo'])) ? $_REQUEST['Tipo'] : false;
        $query = 'SELECT AR.*,
            (SELECT GROUP_CONCAT(F.Factura SEPARATOR " / ")
                FROM Factura_Acta_Recepcion F
                WHERE F.Id_Acta_Recepcion=AR.Id_Acta_Recepcion
                GROUP BY F.Id_Acta_Recepcion
            ) AS Factura,
            B.Nombre as Nombre_Bodega,
            (SELECT P.Nombre FROM Proveedor P WHERE P.Id_Proveedor=AR.Id_Proveedor) AS Proveedor,
            (SELECT PAR.Codigo_Compra
                FROM Producto_Acta_Recepcion PAR
                WHERE PAR.Id_Acta_Recepcion=AR.Id_Acta_Recepcion
                GROUP BY AR.Id_Acta_Recepcion
            ) AS Codigo_Compra
            FROM Acta_Recepcion AR
            INNER JOIN Bodega_Nuevo B ON AR.Id_Bodega_Nuevo=B.Id_Bodega_Nuevo
            WHERE AR.Id_Acta_Recepcion=' . $id_acta;

        $oCon = new consulta();
        $oCon->setTipo('Multiple');
        $oCon->setQuery($query);
        $datos = $oCon->getData();
        unset($oCon);

        $query = 'SELECT Factura,
            Fecha_Factura,
            Archivo_Factura
            FROM Factura_Acta_Recepcion
            WHERE Id_Acta_Recepcion=' . $id_acta;

        $oCon = new consulta();
        $oCon->setTipo('Multiple');
        $oCon->setQuery($query);
        $facturas = $oCon->getData();
        unset($oCon);

        if (!$tipo) {
            $query3 = 'SELECT AR.*,
                B.Nombre as Nombre_Bodega,
                P.Nombre as NombreProveedor,
                P.Direccion as DireccionProveedor,
                P.Telefono as TelefonoProveedor,
                (SELECT PAR.Codigo_Compra
                    FROM Producto_Acta_Recepcion PAR
                    WHERE PAR.Id_Acta_Recepcion=AR.Id_Acta_Recepcion
                    GROUP BY AR.Id_Acta_Recepcion
                ) AS Codigo_Compra
                FROM Acta_Recepcion AR
                INNER JOIN Bodega_Nuevo B ON AR.Id_Bodega_Nuevo =B.Id_Bodega_Nuevo
                INNER JOIN Proveedor P On P.Id_Proveedor = AR.Id_Proveedor
                WHERE AR.Id_Acta_Recepcion=' . $id_acta;
        } else {
            $query3 = 'SELECT AR.*,
                PD.Nombre as Nombre_Bodega,
                P.Nombre as NombreProveedor,
                P.Direccion as DireccionProveedor,
                P.Telefono as TelefonoProveedor,
                (SELECT PAR.Codigo_Compra
                    FROM Producto_Acta_Recepcion PAR
                    WHERE PAR.Id_Acta_Recepcion=AR.Id_Acta_Recepcion
                    GROUP BY AR.Id_Acta_Recepcion
                ) AS Codigo_Compra
                FROM Acta_Recepcion AR
                INNER JOIN Punto_Dispensacion PD ON AR.Id_Punto_Dispensacion=PD.Id_Punto_Dispensacion
                INNER JOIN Proveedor P On P.Id_Proveedor = AR.Id_Proveedor
                WHERE AR.Id_Acta_Recepcion=' . $id_acta;
        }

        $oCon = new consulta();
        //$oCon->setTipo('Multiple');
        $oCon->setQuery($query3);
        $datos2 = $oCon->getData();
        unset($oCon);

        $query2 = 'SELECT P.*,
            PRD.Nombre_Comercial,
            IFNULL(CONCAT(PRD.Presentacion," ", PRD.Cantidad," ", PRD.Unidad_Medida),
            CONCAT(PRD.Nombre_Comercial)) as Nombre_Producto,
            IFNULL(POC.Cantidad,0) as Cantidad_Solicitada
            FROM Producto_Acta_Recepcion P
            INNER JOIN Producto PRD ON P.Id_Producto=PRD.Id_Producto
            LEFT JOIN Producto_Orden_Compra_Nacional POC ON POC.Id_Producto_Orden_Compra_Nacional = P.Id_Producto_Orden_compra
            WHERE P.Id_Acta_Recepcion=' . $id_acta;

        $oCon = new consulta();
        $oCon->setTipo('Multiple');
        $oCon->setQuery($query2);
        $productos_acta = $oCon->getData();
        unset($oCon);


        $id = "";
        $lotes = [];
        $i = -1;
        $j = -1;
        $k = -1;
        $subtotal = 0;
        $impuesto = 0;
        $total = 0;
        foreach ($productos_acta as $prod) {
            $j++;
            if ($id != $prod->Id_Producto) {
                $id = $prod->Id_Producto;
                if ($j > 0) {
                    $k++;
                    $productos_final[$k] = $productos_acta[$j - 1];
                    $productos_final[$k]["Lotes"] = $lotes;
                }
                $lotes = [];
                $i = 0;
                $lotes[$i]["Lote"] = $prod->Lote;
                $lotes[$i]["Fecha_Vencimiento"] = $prod->Fecha_Vencimiento;
                $lotes[$i]["Cantidad"] = (int)$prod->Cantidad;
                $lotes[$i]["Precio"] = $prod->Precio;
                $lotes[$i]["Impuesto"] = (int)$prod->Impuesto;
                $lotes[$i]["Subtotal"] = $prod->Subtotal;
            } else {
                $i++;
                $lotes[$i]["Lote"] = $prod->Lote;
                $lotes[$i]["Fecha_Vencimiento"] = $prod->Fecha_Vencimiento;
                $lotes[$i]["Cantidad"] = (int)$prod->Cantidad;
                $lotes[$i]["Precio"] = $prod->Precio;
                $lotes[$i]["Impuesto"] = (int)$prod->Impuesto;
                $lotes[$i]["Subtotal"] = $prod->Subtotal;
            }
            $subtotal += (int)$prod->Cantidad * $prod->Precio;
            $impuesto += ((int)$prod->Cantidad * $prod->Precio * ((int)$prod->Impuesto / 100));
        }
        $total += $subtotal + $impuesto;
        $productos_final[$k + 1] = $prod;
        $productos_final[$k + 1]->Lotes = $lotes;
        $resultado = [];

        $resultado["Datos"] = $datos;
        $resultado["Datos2"] = $datos2;
        $resultado["Datos2"]["ConteoProductos"] = count($productos_final);
        $resultado["Datos2"]["Subtotal"] = $subtotal;
        $resultado["Datos2"]["Impuesto"] = $impuesto;
        $resultado["Datos2"]["Total"] = $total;

        $resultado["Productos"] = $productos_acta;
        $resultado["ProductosNuevo"] = $productos_final;

        $resultado["Facturas"] = $facturas;


        echo json_encode($resultado);
    }

    public function getActividadesActa()
    {
        $id_acta = (isset($_REQUEST['id_acta']) ? $_REQUEST['id_acta'] : '');

        //Consultar el codigo del acta y el id de la orden de compra
        $query_id_codigo = 'SELECT
                            Id_Orden_Compra_Nacional
                        FROM Acta_Recepcion
                        WHERE
                            Id_Acta_Recepcion = ' . $id_acta;

        $oCon = new consulta();
        $oCon->setQuery($query_id_codigo);
        $id_codigo = $oCon->getData();
        unset($oCon);

        $query = 'SELECT
				AOC.*,
				F.image,
				IFNULL((CASE
				    WHEN AOC.Estado="Creacion" THEN CONCAT("1 ",AOC.Estado)
				    WHEN AOC.Estado="Recepcion" THEN CONCAT("2 ",AOC.Estado)
				    WHEN AOC.Estado="Edicion" THEN CONCAT("2 ",AOC.Estado)
				    WHEN AOC.Estado="Aprobacion" THEN CONCAT("2 ",AOC.Estado)
				END), "0 Sin Estado") as Estado_Actividad
			FROM Actividad_Orden_Compra AOC
			INNER JOIN people F ON AOC.Identificacion_Funcionario=F.identifier
			WHERE
				AOC.Id_Orden_Compra_Nacional = ' . $id_codigo['Id_Orden_Compra_Nacional'] . '
			Order BY Fecha ASC';

        try {

            $queryObj = new QueryBaseDatos($query);
            $result = $queryObj->Consultar('Multiple');

            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode($e);
        }
    }
}
