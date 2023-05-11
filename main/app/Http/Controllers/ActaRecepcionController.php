<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Services\consulta;
use App\Http\Services\QueryBaseDatos;
use App\Http\Services\PaginacionData;
use App\Http\Services\complex;
use App\Http\Services\Costo_Promedio;
use App\Http\Services\Contabilizar;
use App\Http\Services\Configuracion;
use App\Http\Services\conf;
use App\Http\Services\HttpResponse;
use App\Models\ActaRecepcion;
use App\Models\CausalAnulacion;
use App\Models\FacturaActaRecepcion;
use App\Models\ProductoActaRecepcion;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ActaRecepcionController extends Controller
{
    use ApiResponser;

    public function listaImpuestoMes()
    {
        $query = 'SELECT * FROM Impuesto ';

        $oCon = new consulta();
        $oCon->setTipo('Multiple');
        $oCon->setQuery($query);
        $resultado['Impuesto'] = $oCon->getData();
        unset($oCon);

        $query = 'SELECT Meses_Vencimiento FROM Configuracion ';

        $oCon = new consulta();
        $oCon->setQuery($query);
        $resultado['Meses'] = $oCon->getData();
        unset($oCon);

        $query = 'SELECT Id_Bodega_Nuevo, Nombre  FROM Bodega_Nuevo ';

        $oCon = new consulta();
        $oCon->setTipo('Multiple');
        $oCon->setQuery($query);
        $resultado['Bodega'] = $oCon->getData();
        unset($oCon);

        return response()->json($resultado);
    }

    public function listaSubcategorias()
    {
        $id = isset($_REQUEST['id_bodega']) ? $_REQUEST['id_bodega'] : false;
        $query = 'SELECT S.*, C.Nombre As Categoria_Nueva FROM Subcategoria S
            INNER JOIN Categoria_Nueva C ON S.Id_Categoria_Nueva = C.Id_Categoria_Nueva
            ORDER BY Id_Categoria_Nueva ';
        $oCon = new consulta();
        $oCon->setTipo('Multiple');
        $oCon->setQuery($query);
        $resultado = $oCon->getData();
        $resultado = $this->separarPorEstiba($resultado);
        unset($oCon);
        return response()->json($resultado);
    }

    public function validateActa($id) {
        return $this->success(ActaRecepcion::with('facturas', 'products')->where('Id_Orden_Compra_Nacional', $id)->first());
    }

    function separarPorEstiba($resultado)
    {
        $porCategorias = [];
        $porCategorias[0]['Categoria_Nueva'] = $resultado[0]->Categoria_Nueva;
        $porCategorias[0]['Subcategorias'] = [];

        $XEstiba = 0; //index por Estiba
        $XProducto = 0; //index Por Producto

        foreach ($resultado as $key => $categorias) {

            if ($porCategorias[$XEstiba]['Categoria_Nueva'] == $categorias->Categoria_Nueva) {
                $porCategorias[$XEstiba]['Subcategorias'][$XProducto]['Nombre_Subcategoria'] = $categorias->Nombre;
                $porCategorias[$XEstiba]['Subcategorias'][$XProducto]['Id_Subcategoria'] = $categorias->Id_Subcategoria;
                $XProducto++;
            } else {
                $XEstiba++;
                $XProducto = 0;
                $porCategorias[$XEstiba]['Categoria_Nueva'] = $categorias->Categoria_Nueva;
                $porCategorias[$XEstiba]['Subcategorias'][$XProducto]['Nombre_Subcategoria'] = $categorias->Nombre;

                $porCategorias[$XEstiba]['Subcategorias'][$XProducto]['Id_Subcategoria'] = $categorias->Id_Subcategoria;
                $XProducto++;
            }
        }
        return $porCategorias;
    }

    public function listarPendientes()
    {
        $actaRecepcion = ActaRecepcion::select(
            '*',
            DB::raw('( SELECT GROUP_CONCAT(Factura) FROM Factura_Acta_Recepcion WHERE Id_Acta_Recepcion = Acta_Recepcion.Id_Acta_Recepcion ) as Facturas')
        )
            ->with('person')
            ->with(['orden' => function ($q) {
                $q->where('Id_Bodega_Nuevo', '<>', null);
            }])
            ->with('bodega:Id_Bodega_Nuevo,Nombre')
            ->where('Acta_Recepcion.Estado', '=', 'Pendiente')
            ->orderBy('Fecha_Creacion', 'desc')
            ->orderBy('Codigo', 'desc')
            ->get();


        return response()->json($actaRecepcion);
    }

    public function listarAnuladas(Request $request)
    {
        return $this->success(ActaRecepcion::with('orden', 'causal', 'third')
            ->where('Estado', 'Anulada')
            ->when($request->codigo, function ($q, $fill) {
                $q->where('Codigo', 'like', "%$fill%");
            })
            ->paginate(request()->get('tam', 10), ['*'], 'page', request()->get('pag', 1)));
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
            B.Nombre as Bodega,  B.Id_Bodega_Nuevo AS Id_Bodega_Nuevo ,  OCN.Codigo as Codigo_Compra_N, IFNULL(P.social_reason, CONCAT_WS(" ", P.first_name, P.first_surname)) as Proveedor,
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

            INNER JOIN third_parties P
            ON P.id = ARC.Id_Proveedor

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

    public function detalleActa(Request $request)
    {
        return $this->success(
            ActaRecepcion::with(
                'bodega',
                'person',
                'third',
                'causal',
                'facturas',
                'orden.activity',
                'products.product.unit',
                'products.product.packaging',
                'products.product.tax'
            )
                ->find($request->id)
        );
        /* $id_acta = (isset($_REQUEST['id']) ? $_REQUEST['id'] : '');
        $tipo = (isset($_REQUEST['Tipo'])) ? $_REQUEST['Tipo'] : false;
        $query = 'SELECT AR.*,
            (SELECT GROUP_CONCAT(F.Factura SEPARATOR " / ")
                FROM Factura_Acta_Recepcion F
                WHERE F.Id_Acta_Recepcion=AR.Id_Acta_Recepcion
                GROUP BY F.Id_Acta_Recepcion
            ) AS Factura,
            B.Nombre as Nombre_Bodega,
            (SELECT IFNULL(P.social_reason, CONCAT_WS(" ", P.first_name, P.first_surname)) FROM third_parties P WHERE P.id=AR.Id_Proveedor) AS Proveedor,
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
                IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname)) as NombreProveedor,
                P.cod_dian_address as DireccionProveedor,
                P.landline as TelefonoProveedor,
                (SELECT PAR.Codigo_Compra
                    FROM Producto_Acta_Recepcion PAR
                    WHERE PAR.Id_Acta_Recepcion=AR.Id_Acta_Recepcion
                    GROUP BY AR.Id_Acta_Recepcion
                ) AS Codigo_Compra
                FROM Acta_Recepcion AR
                INNER JOIN Bodega_Nuevo B ON AR.Id_Bodega_Nuevo =B.Id_Bodega_Nuevo
                INNER JOIN third_parties P On P.id = AR.Id_Proveedor
                WHERE AR.Id_Acta_Recepcion=' . $id_acta;
        } else {
            $query3 = 'SELECT AR.*,
                PD.Nombre as Nombre_Bodega,
                IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname)) as NombreProveedor,
                P.cod_dian_address as DireccionProveedor,
                P.landline as TelefonoProveedor,
                (SELECT PAR.Codigo_Compra
                    FROM Producto_Acta_Recepcion PAR
                    WHERE PAR.Id_Acta_Recepcion=AR.Id_Acta_Recepcion
                    GROUP BY AR.Id_Acta_Recepcion
                ) AS Codigo_Compra
                FROM Acta_Recepcion AR
                INNER JOIN Punto_Dispensacion PD ON AR.Id_Punto_Dispensacion=PD.Id_Punto_Dispensacion
                INNER JOIN third_parties P On P.id = AR.Id_Proveedor
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
        //$productos_final[$k + 1] = $prod;
        //$productos_final[$k + 1]->Lotes = $lotes;
        $resultado = [];

        $resultado["Datos"] = $datos;
        $resultado["Datos2"] = $datos2;
        //$resultado["Datos2"]["ConteoProductos"] = count($productos_final);
        $resultado["Datos2"]["Subtotal"] = $subtotal;
        $resultado["Datos2"]["Impuesto"] = $impuesto;
        $resultado["Datos2"]["Total"] = $total;

        $resultado["Productos"] = $productos_acta;
        //$resultado["ProductosNuevo"] = $productos_final;

        $resultado["Facturas"] = $facturas;


        return response()->json($resultado); */
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

    public function save(Request $request)
    {
        try {
            //$data = $request->all();

            $data = $request->except('invoices', 'products', 'products_acta', 'invoices');
            //dd($data);
            $products_acta = $request->selected_products;
            //dd($products_acta);
            $invoices = $request->invoices;
            //dd($invoices);

            $data['Identificacion_Funcionario'] = auth()->user()->id;

            if (!$request->id) {
                $data['Codigo'] = generateConsecutive('acta_recepcion');
            }
            $acta = ActaRecepcion::updateOrCreate(
                ['Id_Acta_Recepcion' => $data['Id_Acta_Recepcion']], $data
            );
            foreach ($invoices as $invoice) {
                $factura = FacturaActaRecepcion::updateOrCreate(
                    ['Id_Factura_Acta_Recepcion' => $invoice['Id_Factura_Acta_Recepcion']],
                    [
                        'Id_Acta_Recepcion' => $acta['Id_Acta_Recepcion'], //este no sirve
                        'Factura' => $invoice['Factura'],
                        'Fecha_Factura' => $invoice['Fecha_Factura'],
                        'Archivo_Factura' => $invoice['Archivo_Factura'],
                        'Id_Orden_Compra' => $data['Id_Orden_Compra_Nacional'],
                        'Tipo_Compra' => $data['Tipo'],
                    ]
                );
            }

            foreach ($products_acta as $product) {
                ProductoActaRecepcion::updateOrCreate(
                    ['Id_Producto_Acta_Recepcion' => $product['Id_Producto_Acta_Recepcion']],
                    [
                        'Id_Acta_Recepcion' => $acta['Id_Acta_Recepcion'], //este no sirve
                        'Factura' => $factura['Id_Factura_Acta_Recepcion'],
                        'Cantidad' => $product['Cantidad'],
                        'Precio' => $product['Total'],
                        'Impuesto' => $product['Iva'],
                        'Subtotal' => $product['Subtotal'],
                        'Id_Producto' => $product['Id_Producto'],
                        'Tipo_Compra' => $data['Tipo'],
                        'Id_Producto_Orden_Compra' => $data['Id_Orden_Compra_Nacional'],
                    ]
                );
            }
            if (!$request->id) {
                sumConsecutive('acta_recepcion');
            }
            return $this->success('Creado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function saveOld()
    {
        $company_id = 1;

        //Objeto de la clase que almacena los archivos
        //$storer = new FileStorer();
        //echo 'asd';exit;
        $contabilizar = new Contabilizar();
        $configuracion = new Configuracion($company_id);

        $datos = (isset($_REQUEST['datos']) ? $_REQUEST['datos'] : '');
        $productoCompra = (isset($_REQUEST['productos']) ? $_REQUEST['productos'] : '');
        $codigoCompra = (isset($_REQUEST['codigoCompra']) ? $_REQUEST['codigoCompra'] : '');
        $tipoCompra = (isset($_REQUEST['tipoCompra']) ? $_REQUEST['tipoCompra'] : '');
        $facturas = (isset($_REQUEST['facturas']) ? $_REQUEST['facturas'] : '');
        $comparar = (isset($_REQUEST['comparar']) ? $_REQUEST['comparar'] : '');
        $archivos = (isset($_REQUEST['archivos']) ? $_REQUEST['archivos'] : '');
        $no_conforme_devolucion = (isset($_REQUEST['id_no_conforme']) ? $_REQUEST['id_no_conforme'] : false);

        $datosProductos = (array) json_decode($productoCompra, true);
        $datos = (array) json_decode($datos);
        $facturas = (array) json_decode($facturas, true);
        $comparar = (array) json_decode($comparar, true);

        $datos_movimiento_contable = array();


        $prov_ret = $this->GetInfoRetencionesProveedor($datos['Id_Proveedor']);
        $columns = array_column($facturas, 'Retenciones');

        if (/* $prov_ret['Tipo_Retencion'] == 'Permanente' || */$prov_ret['Tipo_Reteica'] == 'Permanente') {
            if ($prov_ret['Id_Plan_Cuenta_Retefuente'] != '' || $prov_ret['Id_Plan_Cuenta_Reteica'] != '') {
                if (count($columns) == 0) {
                    $resultado['mensaje'] = "Ha ocurrido un incoveniente con las retenciones de las facturas cargadas, contacte con el administrador del sistema!";
                    $resultado['tipo'] = "error";

                    echo json_encode($resultado);
                    exit;
                }
            }
        }

        $cod = $configuracion->getConsecutivo('Acta_Recepcion', 'Acta_Recepcion');

        $datos['Codigo'] = $cod;

        $estado = $this->ValidarCodigo($cod, $company_id);
        if ($estado) {
            $cod = $configuracion->getConsecutivo('Acta_Recepcion', 'Acta_Recepcion');
            $datos['Codigo'] = $cod;
        }

        $oCon = new consulta();

        switch ($tipoCompra) {

            case "Nacional": {
                    $query = "SELECT Id_Orden_Compra_Nacional as id, Codigo, Identificacion_Funcionario, Id_Bodega_Nuevo, Id_Proveedor
                        FROM Orden_Compra_Nacional WHERE Codigo = '" . $codigoCompra . "'";

                    $oCon->setQuery($query);
                    $detalleCompra = $oCon->getData();
                    $oCon->setTipo('Multiple');
                    unset($oCon);
                    break;
                }
            case "Internacional": {

                    $query = "SELECT Id_Orden_Compra_Internacional as id,Codigo,I
                dentificacion_Funcionario,Id_Bodega_Nuevo,Id_Proveedor FROM Orden_Compra_Internacional
                WHERE Codigo = '" . $codigoCompra . "'";
                    $oCon->setQuery($query);
                    $detalleCompra = $oCon->getData();
                    $oCon->setTipo('Multiple');
                    unset($oCon);
                    break;
                }
        }




        $oItem = new complex("Acta_Recepcion", "Id_Acta_Recepcion");
        foreach ($datos as $index => $value) {
            $oItem->$index = $value;
        }
        if ($datos['Tipo'] == 'Nacional') {
            $oItem->Id_Orden_Compra_Nacional = $datos['Id_Orden_Compra'];
        } else {
            $oItem->Id_Orden_Compra_Internacional = $datos['Id_Orden_Compra'];
        }

        $oItem->save();
        $id_Acta_Recepcion = $oItem->getId();
        unset($oItem);
        /* AQUI GENERA QR */
        $qr = $this->generarqr('actarecepcion', $id_Acta_Recepcion, '/IMAGENES/QR/');
        $oItem = new complex("Acta_Recepcion", "Id_Acta_Recepcion", $id_Acta_Recepcion);
        $oItem->Codigo_Qr = $qr;
        $oItem->save();
        unset($oItem);
        /* HASTA AQUI GENERA QR */


        // productos

        /* var_dump($detalleCompra);
        exit; */

        // realizar guardado para las caracteristicas de los productos
        //1. revisar cuales fueron marcados y no marcados en el array que traigo.
        $i = -1;
        $contador = 0;
        $genero_no_conforme = false;
        $id_no_conforme = '';
        $productos = explode(",", $comparar['Id_Producto']);
        $id_productos_acta = array_column($datosProductos, "Id_Producto");
        //print_r( $productos );
        //rint_r( $productos );
        //print_r( $datosProductos );



        foreach ($productos as $value) {

            if (!array_search($value, $id_productos_acta)) {


                if (!$genero_no_conforme) {

                    $genero_no_conforme = true;
                    $configuracion = new Configuracion($company_id);
                    $cod = $configuracion->getConsecutivo('No_Conforme', 'No_Conforme');
                    $oItem2 = new complex('No_Conforme', 'Id_No_Conforme');
                    $oItem2->Codigo = $cod;
                    $oItem2->Persona_Reporta = $datos['Identificacion_Funcionario'];
                    $oItem2->Tipo = "Compra";
                    $oItem2->Id_Acta_Recepcion_Compra = $id_Acta_Recepcion;
                    $oItem2->save();
                    $id_no_conforme = $oItem2->getId();
                    unset($oItem2);

                    /*AQUI GENERA QR */
                    /*$qr = generarqr('noconforme',$id_no_conforme,'/IMAGENES/QR/');
                    $oItem = new complex("No_Conforme","Id_No_Conforme",$id_no_conforme);
                    $oItem->Codigo_Qr=$qr;
                    $oItem->save();
                    unset($oItem);*/
                    /* HASTA AQUI GENERA QR */
                }
                /*var_dump( [$comparar['Id_Orden_Nacional'], $value ] )  ;exit;
                exit;*/
                $query = "SELECT	Cantidad  FROM Producto_Orden_Compra_Nacional WHERE Id_Producto = " . $value . " AND Id_Orden_Compra_Nacional=" . $comparar['Id_Orden_Nacional'];

                $oCon = new consulta();
                $oCon->setQuery($query);
                $cantidad = $oCon->getData();
                unset($oCon);

                $oItem2 = new complex('Producto_No_Conforme', 'Id_Producto_No_Conforme');
                $oItem2->Id_Producto = $value;
                $oItem2->Id_No_Conforme = $id_no_conforme;
                $oItem2->Id_Compra = $datos["Id_Orden_Compra"];
                $oItem2->Tipo_Compra = $datos["Tipo"];
                $oItem2->Id_Acta_Recepcion = $id_Acta_Recepcion;
                $oItem2->Cantidad = number_format($cantidad['Cantidad'], 0, "", "");
                $oItem2->Id_Causal_No_Conforme = 2;
                $oItem2->Observaciones = "PRODUCTO NO LLEGO EN FISICO";
                $oItem2->save();
                unset($oItem2);
            }
        }
        //echo 'er444';exit;

        foreach ($datosProductos as $prod) {
            unset($prod["producto"][count($prod["producto"]) - 1]);
            foreach ($prod["producto"] as $item) {
                $i++;

                if ($item['Lote'] != '') {

                    $oItem = new complex('Producto_Acta_Recepcion', 'Id_Producto_Acta_Recepcion');
                    //mandar productos a Producto_Acta_Recepcion

                    foreach ($item as $index => $value) {

                        if ($index == 'Temperatura') {
                            $oItem->$index = number_format($value, 2, ".", "");
                        } else {
                            $oItem->$index = $value;
                        }
                    }
                    $subtotal = ((int) $item['Cantidad']) * ((int) $item['Precio']);
                    $oItem->Id_Producto_Orden_Compra = $prod["Id_Producto_Orden_Compra"];
                    $oItem->Codigo_Compra = $codigoCompra;
                    $oItem->Tipo_Compra = $tipoCompra;
                    $oItem->Id_Acta_Recepcion = $id_Acta_Recepcion;
                    // $precio = number_format($prod['Precio'],2,".","");
                    $subtotal = number_format((int)$subtotal, 2, ".", "");
                    $oItem->Subtotal = round($subtotal);
                    $oItem->save();
                    unset($oItem);
                }


                if ($item["No_Conforme"] != "") {
                    if (!$genero_no_conforme) { // Para que solo registre un solo registro por cada no conforme de productos.
                        $genero_no_conforme = true;
                        $configuracion = new Configuracion($company_id);
                        $cod = $configuracion->getConsecutivo('No_Conforme', 'No_Conforme');
                        $oItem2 = new complex('No_Conforme', 'Id_No_Conforme');
                        $oItem2->Codigo = $cod;
                        $oItem2->Persona_Reporta = $datos['Identificacion_Funcionario'];
                        $oItem2->Tipo = "Compra";
                        $oItem2->Id_Acta_Recepcion_Compra = $id_Acta_Recepcion;
                        $oItem2->save();
                        $id_no_conforme = $oItem2->getId();
                        unset($oItem2);

                        /*AQUI GENERA QR */
                        $qr = $this->generarqr('noconforme', $id_no_conforme, '/IMAGENES/QR/');
                        $oItem = new complex("No_Conforme", "Id_No_Conforme", $id_no_conforme);
                        $oItem->Codigo_Qr = $qr;
                        $oItem->save();
                        unset($oItem);
                        /*HASTA AQUI GENERA QR */
                    }

                    $oItem2 = new complex('Producto_No_Conforme', 'Id_Producto_No_Conforme');
                    $oItem2->Id_Producto = $item["Id_Producto"];
                    $oItem2->Id_No_Conforme = $id_no_conforme;
                    $oItem2->Id_Compra = $datos["Id_Orden_Compra"];
                    $oItem2->Tipo_Compra = $datos["Tipo"];
                    $oItem2->Id_Acta_Recepcion = $id_Acta_Recepcion;
                    $oItem2->Cantidad = $item["Cantidad_No_Conforme"];
                    $oItem2->Id_Causal_No_Conforme = $item['No_Conforme'];
                    $oItem = new complex("Causal_No_Conforme", "Id_Causal_No_Conforme", $item['No_Conforme']);
                    $oItem2->Observaciones = $oItem->Nombre;
                    unset($oItem);
                    $oItem2->save();
                    unset($oItem2);
                }

                // ACA SE AGREGA EL PRODUCTO A LA LISTA DE GANANCIA
                $query = 'SELECT LG.Porcentaje, LG.Id_Lista_Ganancia FROM Lista_Ganancia LG';
                $oCon = new consulta();
                $oCon->setQuery($query);
                $oCon->setTipo('Multiple');
                $porcentaje = $oCon->getData();
                unset($oCon);
                //datos
                //$cum_producto = $this->GetCodigoCum($item['Id_Producto']);
                foreach ($porcentaje as  $value) {
                    $query = 'SELECT * FROM Producto_Lista_Ganancia WHERE Id_lista_Ganancia=' . $value->Id_Lista_Ganancia;
                    $oCon = new consulta();
                    $oCon->setQuery($query);
                    $cum = $oCon->getData();
                    unset($oCon);
                    if ($cum) {
                        $precio = number_format($item['Precio'] / ((100 - $value->Porcentaje) / 100), 0, '.', '');
                        if ($cum['Precio'] < $precio) {
                            $oItem = new complex('Producto_Lista_Ganancia', 'Id_Producto_Lista_Ganancia', $cum['Id_Producto_Lista_Ganancia']);

                            $oItem->Precio = $precio;
                            $oItem->Id_Lista_Ganancia = $value->Id_Lista_Ganancia;
                            $oItem->save();
                            unset($oItem);
                        }
                    } else {
                        $oItem = new complex('Producto_Lista_Ganancia', 'Id_Producto_Lista_Ganancia');
                        //$oItem->Cum = $cum_producto;
                        $precio = number_format($item['Precio'] / ((100 - $value->Porcentaje) / 100), 0, '.', '');
                        $oItem->Precio = $precio;
                        $oItem->Id_Lista_Ganancia = $value->Id_Lista_Ganancia;
                        $oItem->save();
                        unset($oItem);
                    }
                }
            }
        }


        //NUEVO METODO PARA GUARDAR LOS ARCHIVOS
        // if (!empty($_FILES) && count($_FILES) > 0){
        // $facturas_files = array();
        // $productos_files = array();
        // foreach ($_FILES as $key => $value) {
        //     if (strpos($key, 'archivos')) {
        //         array_push($facturas_files, $value);
        //     }else{
        //         array_push($productos_files, $value);
        //     }
        // }
        //     //GUARDAR ARCHIVO Y RETORNAR NOMBRE DEL MISMO
        //     $nombres_archivos = $storer->UploadFileToRemoteServer($facturas_files, 'store_remote_files', 'ARCHIVOS/FACTURAS_COMPRA/');
        // }

        // Agregando facturas
        $i = -1;
        if ($facturas[count($facturas) - 1]["Factura"] == "") {
            unset($facturas[count($facturas) - 1]);
        }
        foreach ($facturas as $fact) {
            $i++;
            $oItem = new complex('Factura_Acta_Recepcion', 'Id_Factura_Acta_Recepcion');
            $oItem->Id_Acta_Recepcion = $id_Acta_Recepcion;
            $oItem->Factura = $fact["Factura"];
            $oItem->Fecha_Factura = $fact["Fecha_Factura"];

            if (!empty($_FILES["archivos$i"]['name'])) {
                $posicion1 = strrpos($_FILES["archivos$i"]['name'], '.') + 1;
                $extension1 =  substr($_FILES["archivos$i"]['name'], $posicion1);
                $extension1 =  strtolower($extension1);
                $_filename1 = uniqid() . "." . $extension1;

                $_file1 = Storage::put("ARCHIVOS/FACTURAS_COMPRA/", $_filename1);

                $subido1 = move_uploaded_file($_FILES["archivos$i"]['tmp_name'], $_file1);
                if ($subido1) {
                    @chmod($_file1, 0777);
                    $oItem->Archivo_Factura = $_filename1;
                }
            }

            //HABILITAR ESTA LINEA PARA COLOCAR EL NOMBRE DEL ARCHIVO DESDE EL ARRAY QUE VIENE DEL NUEVO METODO PARA GUARDAR LOS ARCHIVOS
            // $oItem->Archivo_Factura = $nombres_archivos[$i];

            $oItem->Id_Orden_Compra = $datos['Id_Orden_Compra'];
            $oItem->Tipo_Compra = $datos['Tipo'];
            $oItem->save();
            $id_factura = $oItem->getId();
            unset($oItem);

            if (count($fact['Retenciones']) > 0) {

                foreach ($fact['Retenciones'] as $rt) {

                    // $oItem = new complex("Factura_Acta_Recepcion_Retencion","Id_Factura_Acta_Recepcion_Retencion");
                    // $oItem->Id_Factura = $id_factura;
                    // $oItem->Id_Retencion =$rt['Id_Retencion'];
                    // $oItem->Id_Acta_Recepcion = $id_Acta_Recepcion;
                    // $oItem->Valor_Retencion = number_format(floatval($rt['Valor']),2,".","");
                    // $oItem->save();
                    // unset($oItem);

                    if ($rt['Valor'] > 0) {
                        $oItem = new complex("Factura_Acta_Recepcion_Retencion", "Id_Factura_Acta_Recepcion_Retencion");
                        $oItem->Id_Factura = $id_factura;
                        $oItem->Id_Retencion = $rt['Id_Retencion'];
                        $oItem->Id_Acta_Recepcion = $id_Acta_Recepcion;
                        $oItem->Valor_Retencion = $rt['Valor'] != 0 ? round($rt['Valor'], 0) : '0';
                        $oItem->save();
                        unset($oItem);
                    }
                }
            }
        }

        // Actualizando datos del producto
        $h = -1;
        foreach ($datosProductos as $value) {
            $h++;
            $oItem = new complex('Producto', 'Id_Producto', $value["Id_Producto"]);

            if ($oItem->Id_Subcategoria != $value["Id_Subcategoria"]) {
                $oItem->Id_Subcategoria = $value["Id_Subcategoria"];
            }
            if (isset($_FILES["fotos$h"]['name'])) {
                $posicion2 = strrpos($_FILES["fotos$h"]['name'], '.') + 1;
                $extension2 =  substr($_FILES["fotos$h"]['name'], $posicion2);
                $extension2 =  strtolower($extension2);
                $_filename2 = uniqid() . "." . $extension2;
                $_file2 = Storage::put("IMAGENES/PRODUCTOS/", $_filename2);

                $subido2 = move_uploaded_file($_FILES["fotos$h"]['tmp_name'], $_file2);
                if ($subido2) {
                    @chmod($_file2, 0777);
                    $oItem->Imagen = $_filename2;
                }
            }
            $oItem->save();
            unset($oItem);
        }

        //cambiar el estado de la compra a RECIBIDA
        switch ($tipoCompra) {

            case "Nacional": {
                    $oItem = new complex('Orden_Compra_Nacional', 'Id_Orden_Compra_Nacional', $detalleCompra['id']);
                    $oItem->getData();
                    $oItem->Estado = "Recibida";
                    $oItem->save();
                    unset($oItem);
                    break;
                }
            case "Internacional": {
                    $oItem = new complex('Orden_Compra_Internacional', 'Id_Orden_Compra_Internacional', $detalleCompra['id']);
                    $oItem->getData();
                    $oItem->Estado = "Recibida";
                    $oItem->save();
                    unset($oItem);
                    break;
                }
        }
        if ($contador == 0) {
            $resultado['mensaje'] = "Se ha guardado correctamente el acta de recepcion";
            $resultado['tipo'] = "success";
        } else {
            $resultado['mensaje'] = "Se ha guardado correctamente el acta de recepcion con los productos No Conformes";
            $resultado['tipo'] = "success";
        }

        //Consultar el codigo del acta y el id de la orden de compra
        $query_codido_acta = 'SELECT
                                    Codigo,
                                    Id_Orden_Compra_Nacional
                                FROM
                                    Acta_Recepcion
                                WHERE
                                    Id_Acta_Recepcion = ' . $id_Acta_Recepcion;

        $oCon = new consulta();
        $oCon->setQuery($query_codido_acta);
        $acta_data = $oCon->getData();
        unset($oCon);

        //Guardando paso en el seguimiento del acta en cuestion
        $oItem = new complex('Actividad_Orden_Compra', "Id_Acta_Recepcion_Compra");
        $oItem->Id_Orden_Compra_Nacional = $acta_data['Id_Orden_Compra_Nacional'];
        $oItem->Id_Acta_Recepcion_Compra = $id_Acta_Recepcion;
        $oItem->Identificacion_Funcionario = $datos['Identificacion_Funcionario'];
        $oItem->Detalles = "Se recibio el acta con codigo " . $acta_data['Codigo'];
        $oItem->Fecha = date("Y-m-d H:i:s");
        $oItem->Estado = 'Recepcion';
        $oItem->save();
        unset($oItem);

        if ($no_conforme_devolucion) {
            $oItem = new complex('No_Conforme', 'Id_No_Conforme', $no_conforme_devolucion);
            $oItem->Estado = 'Cerrado';
            $oItem->save();
            unset($oItem);
        }


        //GUARDAR MOVMIMIENTO CONTABLE ACTA*/
        $datos_movimiento_contable['Id_Registro'] = $id_Acta_Recepcion;
        $datos_movimiento_contable['Numero_Comprobante'] = $datos['Codigo'];
        $datos_movimiento_contable['Nit'] = $datos['Id_Proveedor'];
        $datos_movimiento_contable['Productos'] = $datosProductos;
        $datos_movimiento_contable['Facturas'] = $facturas;

        //$contabilizar->CrearMovimientoContable('Acta Recepcion', $datos_movimiento_contable);

        echo json_encode($resultado);
    }
    function GetCodigoCum($id_producto)
    {


        $query = '
            SELECT
                Codigo_Cum
            FROM Producto
            WHERE
                Id_Producto = ' . $id_producto;

        $oCon = new consulta();
        $oCon->setQuery($query);
        $cum = $oCon->getData();
        unset($oCon);


        return $cum['Codigo_Cum'];
    }

    function ValidarCodigo($codigo, $company_id)
    {
        $estado = false;

        $query = 'SELECT
            Codigo
        FROM Acta_Recepcion
        WHERE
            Codigo = "' . $codigo . '"';

        $oCon = new consulta();
        $oCon->setQuery($query);
        $acta = $oCon->getData();
        unset($oCon);
        if ($acta && $acta['Codigo']) {
            $estado = true;
        }
        return $estado;
    }

    function GetInfoRetencionesProveedor($idProveedor)
    {
        /* retention_type as Tipo_Retencion, */
        $query = "
            SELECT
            reteica_type as Tipo_Reteica,
            reteica_account_id as Id_Plan_Cuenta_Reteica,
            retefuente_account_id as Id_Plan_Cuenta_Retefuente
            FROM third_parties
            WHERE
                id = '$idProveedor'";

        //CONDICIONES ADICIONALES
        // AND (Tipo_Retencion IS NOT NULL AND Tipo_Retencion <> 'N/A' AND Tipo_Retencion <> 'Autorretenedor')
        // AND (Tipo_Reteica IS NOT NULL AND Tipo_Reteica <> 'N/A')
        // AND (Id_Plan_Cuenta_Retefuente IS NOT NULL || Id_Plan_Cuenta_Reteica IS NOT NULL)

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('simple');
        $proveedor = $oCon->getData();
        unset($oCon);

        return $proveedor;
    }
    function generarqr($tipo, $id, $ruta)
    {
        $errorCorrectionLevel = 'H';
        $matrixPointSize = min(max((int)5, 1), 10);
        $nombre = md5($tipo . '|' . $id . '|' . $errorCorrectionLevel . '|' . $matrixPointSize) . '.png';
        $filename = $_SERVER["DOCUMENT_ROOT"] . "/" . $ruta . $nombre;

        return ($nombre);
    }

    public function aprobarActa()
    {
        $id_acta_recepcion = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
        $id_bodega_nuevo = isset($_REQUEST['Id_Bodega_Nuevo']) ? $_REQUEST['Id_Bodega_Nuevo'] : false;
        $funcionario = isset($_REQUEST['funcionario']) ? $_REQUEST['funcionario'] : false;
        try {
            if ($id_acta_recepcion) {
                $oItemA = new complex('Acta_Recepcion', 'Id_Acta_Recepcion', $id_acta_recepcion);
                $acta = $oItemA->getData();
                //actualizar costo promedio
                $query = 'SELECT PA.Id_Producto, PA.Cantidad, PA.Precio,
                         PA.Impuesto,PA.Factura,
                         P.Nombre_Comercial, P.Referencia
                FROM Producto_Acta_Recepcion PA
                INNER JOIN Producto P ON P.Id_Producto = PA.Id_Producto
                WHERE PA.Id_Acta_Recepcion = ' . $id_acta_recepcion;
                $oCon = new consulta();
                $oCon->setQuery($query);
                $oCon->setTipo('Multiple');
                $productos = $oCon->getData();
                unset($oItem);
                //$catalgoS = new CatalogoService();
                //$catalgoS->setActa($acta);
                $paraUbicar = 0;
                foreach ($productos as $key => $producto) {
                    $costopromedio =  new Costo_Promedio($producto["Id_Producto"], $producto["Cantidad"], $producto["Precio"]);
                    $costopromedio->actualizarCostoPromedio();
                    unset($costopromedio);
                    if ($producto['Tipo_Catalogo'] == 'Activo_Fijo') {
                        # code...
                        //$catalgoS->guardarActivoFijo($producto);
                    }
                    if ($producto['Tipo_Catalogo'] == 'Dotacion_EPP') {
                        //$catalgoS->actualizarInventario($producto, $id_bodega_nuevo);
                    }
                    $producto['Ubicar'] == 0 ?  $paraUbicar++ : null;
                }
                $oItemA->Estado =  $paraUbicar == count($productos) ? 'Acomodada' : 'Aprobada';
                $oItemA->save();
                unset($oItemA);
                //Consultar el codigo del acta y el id de la orden de compra
                $query_codido_acta = 'SELECT
                                Codigo,
                                Id_Orden_Compra_Nacional
                            FROM
                                Acta_Recepcion
                            WHERE
                                Id_Acta_Recepcion = ' . $id_acta_recepcion;

                $oCon = new consulta();
                $oCon->setQuery($query_codido_acta);
                $acta_data = $oCon->getData();
                unset($oCon);
                //Guardando paso en el seguimiento del acta en cuestion
                $oItem = new complex('Actividad_Orden_Compra', "Id_Acta_Recepcion_Compra");
                $oItem->Id_Orden_Compra_Nacional = $acta_data['Id_Orden_Compra_Nacional'];
                $oItem->Id_Acta_Recepcion_Compra = $id_acta_recepcion;
                $oItem->Identificacion_Funcionario = $funcionario;
                $oItem->Detalles = "Se aprobo y se ingreso el Acta con codigo " . $acta_data['Codigo'];
                $oItem->Fecha = date("Y-m-d H:i:s");
                $oItem->Estado = 'Aprobacion';
                $oItem->save();
                unset($oItem);
                if ($acta_data) {
                    $resultado['mensaje'] = "Se ha aprobado e ingresado correctamente el acta al inventario";
                    $resultado['tipo'] = "success";
                    $resultado['titulo'] = "Operaci��n Exitosa";
                } else {
                    $resultado['mensaje'] = "Ha ocurrido un error inesperado. Por favor intentelo de nuevo";
                    $resultado['tipo'] = "error";
                    $resultado['titulo'] = "Error";
                }
                return response()->json($resultado);
            }
            //code...
        } catch (\Throwable $th) {

            return response()->json($th->getMessage());
        }
    }

    public function anularActa()
    {
        $queryObj = new QueryBaseDatos();
        $response = array();
        $http_response = new HttpResponse();
        //$contabilizar = new Contabilizar();
        $modelo = (isset($_REQUEST['modelo']) ? $_REQUEST['modelo'] : '');
        $modelo = json_decode($modelo, true);
        $oItem = new complex("Acta_Recepcion", "Id_Acta_Recepcion", $modelo['Id_Acta_Recepcion']);
        $data = $oItem->getData();
        $fecha = date('Y-m-d', strtotime($data['Fecha_Creacion']));
        //if ($contabilizar->validarMesOrAnioCerrado($fecha)) {
        if (true) {
            $oItem->Estado = "Anulada";
            $oItem->Id_Causal_Anulacion = $modelo['Id_Causal_Anulacion'];
            $oItem->Observaciones_Anulacion = $modelo['Observaciones'];
            $oItem->Funcionario_Anula = $modelo['Identificacion_Funcionario'];
            $oItem->Fecha_Anulacion = date("Y-m-d H:i:s");
            $oItem->save();
            unset($oItem);
            $query = 'SELECT *
                FROM  Actividad_Orden_Compra
                WHERE Detalles LIKE "Se recibio el acta%" AND  Id_Acta_Recepcion_Compra = ' . $modelo['Id_Acta_Recepcion'];
            $queryObj->SetQuery($query);
            $actividad = $queryObj->ExecuteQuery('simple');
            $oItem = new complex("Actividad_Orden_Compra", "Id_Actividad_Orden_Compra", $actividad['Id_Actividad_Orden_Compra']);
            $oItem->delete();
            unset($oItem);
            $oItem = new complex("Orden_Compra_Nacional", "Id_Orden_Compra_Nacional", $actividad['Id_Orden_Compra_Nacional']);
            $oItem->Estado = "Pendiente";
            $oItem->save();
            unset($oItem);
            $this->AnularMovimientoContable($modelo['Id_Acta_Recepcion']);
            $http_response->SetRespuesta(0, 'Registro exitoso', 'Se ha anulado correctamente el acta de recepcion');
            $response = $http_response->GetRespuesta();
        } else {
            $http_response->SetRespuesta(3, 'No es posible', 'No es posible anular esta acta debido a que el mes o el año del documento ha sido cerrado contablemente. Si tienes alguna duda por favor comunicarse con el departamento de contabilidad.');
            $response = $http_response->GetRespuesta();
        }

        echo json_encode($response);
    }

    function AnularMovimientoContable($idRegistroModulo)
    {
        //  global $contabilizar;

        //$contabilizar->AnularMovimientoContable($idRegistroModulo, 15);
    }

    public function descargarPdf(Request $request)
    {
        $tipo = $request->tipo;
        $id = $request->id;
    }
}
