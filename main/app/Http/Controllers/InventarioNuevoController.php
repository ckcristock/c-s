<?php

namespace App\Http\Controllers;

use App\Models\InventarioNuevo;
use Illuminate\Http\Request;
use App\Http\Services\consulta;

class InventarioNuevoController extends Controller
{

    public function listar()
    {
        $sin_inventario = (isset($_REQUEST['sin_inventario']) ? $_REQUEST['sin_inventario'] : '');
        $condicion_sin_inventario = '';

        if ($sin_inventario == "false") {
            $condicion_sin_inventario = " AND (I.Cantidad - I.Cantidad_Apartada - I.Cantidad_Seleccionada) > 0";
        } else if ($sin_inventario == "true") {
            $condicion_sin_inventario = "";
        } else if ($sin_inventario == "") {
            $condicion_sin_inventario = "";
        }

        $condicion = '';

        if (isset($_REQUEST['nom']) && $_REQUEST['nom'] != "") {
            $condicion .= " AND (PRD.Principio_Activo LIKE '%$_REQUEST[nom]%' OR PRD.Presentacion LIKE '%$_REQUEST[nom]%' OR PRD.Concentracion LIKE '%$_REQUEST[nom]%' OR PRD.Nombre_Comercial LIKE '%$_REQUEST[nom]%')";
        }



        if (isset($_REQUEST['lab']) && $_REQUEST['lab'] != "") {
            $condicion .= " AND PRD.Laboratorio_Comercial LIKE '%$_REQUEST[lab]%'";
        }

        /* if (isset($_REQUEST['lab_gen']) && $_REQUEST['lab_gen'] != "") {
            $condicion .= " AND PRD.Laboratorio_Generico LIKE '%$_REQUEST[lab_gen]%'";
        } */

        if (isset($_REQUEST['grupo']) && $_REQUEST['grupo'] != "") {
            $condicion .= " AND GE.Nombre LIKE '%$_REQUEST[grupo]%'";
        }


        if (isset($_REQUEST['lote']) && $_REQUEST['lote'] != "") {
            $condicion .= " AND Lote LIKE '%$_REQUEST[lote]%'";
        }

        if (isset($_REQUEST['bod']) && $_REQUEST['bod'] != "") {
            $condicion .= " AND b.Nombre LIKE '%$_REQUEST[bod]%'";
        }


        if (isset($_REQUEST['cant']) && $_REQUEST['cant'] != "") {
            $condicion .= " AND (I.Cantidad-I.Cantidad_Apartada-I.Cantidad_Seleccionada)=$_REQUEST[cant]";
        }

        if (isset($_REQUEST['cant_apar']) && $_REQUEST['cant_apar'] != "") {
            $condicion .= " AND I.Cantidad_Apartada=$_REQUEST[cant_apar]";
        }

        if (isset($_REQUEST['cant_sel']) && $_REQUEST['cant_sel'] != "") {
            $condicion .= " AND I.Cantidad_Seleccionada=$_REQUEST[cant_sel]";
        }


        if (isset($_REQUEST['costo']) && $_REQUEST['costo'] != "") {
            $condicion .= " AND I.Costo=$_REQUEST[costo]";
        }

        if (isset($_REQUEST['invima']) && $_REQUEST['invima'] != "") {
            $condicion .= " AND PRD.Invima LIKE '%$_REQUEST[invima]%'";
        }
        /* if (isset($_REQUEST['lista']) && $_REQUEST['lista'] != "") {
            $condicion .= " AND PLG.Id_Lista_Ganancia=$_REQUEST[lista]";
        } */

        if (isset($_REQUEST['iva']) && $_REQUEST['iva'] != "") {
            $condicion .= " AND PRD.Gravado='$_REQUEST[iva]'";
        }


        if (isset($_REQUEST['fecha']) && $_REQUEST['fecha'] != "") {
            $fecha_inicio = trim(explode(' - ', $_REQUEST['fecha'])[0]);
            $fecha_fin = trim(explode(' - ', $_REQUEST['fecha'])[1]);
            $condicion .= " AND I.Fecha_Vencimiento BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        }

        $condicion_principal = '';

        if (isset($_REQUEST['id']) && ($_REQUEST['id'] != "" && $_REQUEST['id'] != "0")) {
            $condicion_principal = " WHERE SubC.Id_Categoria_Nueva=" . $_REQUEST['id'];
        } else {
            $condicion_principal = " WHERE SubC.Id_Categoria_Nueva!=0";
        }

        if (isset($_REQUEST['id_bodega_nuevo']) && ($_REQUEST['id_bodega_nuevo'] != "" && $_REQUEST['id_bodega_nuevo'] != "0")) {
            $condicion_principal .=  ' AND B.Id_Bodega_Nuevo =' . $_REQUEST['id_bodega_nuevo'];
        } else {
            $condicion_principal .=  ' AND B.Id_Bodega_Nuevo != 0';
        }

        $query = 'SELECT
GROUP_CONCAT(I.Id_Inventario_Nuevo) AS Id_Inventario_Nuevo
FROM
 Inventario_Nuevo I
INNER JOIN Producto PRD ON I.Id_Producto = PRD.Id_Producto
INNER JOIN Estiba E ON E.Id_Estiba = I.Id_Estiba
INNER JOIN Grupo_Estiba GE ON GE.Id_Grupo_Estiba = E.Id_Grupo_Estiba
INNER JOIN Bodega_Nuevo B ON B.Id_Bodega_Nuevo = E.Id_Bodega_Nuevo
INNER JOIN Subcategoria SubC ON PRD.Id_Subcategoria = SubC.Id_Subcategoria
INNER JOIN Categoria_Nueva C ON SubC.Id_Categoria_Nueva = C.Id_Categoria_Nueva
/* LEFT JOIN Producto_Lista_Ganancia PLG ON PRD.Codigo_Cum = PLG.Cum */

' . $condicion_principal . ' ' . $condicion  . $condicion_sin_inventario . ' GROUP BY B.Id_Bodega_Nuevo,
I.Id_Producto,I.Lote,I.Fecha_Vencimiento, I.Codigo_Cum ';



        //$paginationData = new PaginacionData($tam, $query_count, $pag);
        //$queryObj = new QueryBaseDatos($query);
        //$actas_realizadas = $queryObj->Consultar('Multiple', true, $paginationData);


        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $total = $oCon->getData();
        unset($oCon);
        $total =  count($total);


        ####### PAGINACIÓN ########
        $tamPag = 20;
        $numReg = $total;
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


        if ($sin_inventario == "false") {
            $condicion_sin_inventario = " AND (I.Cantidad-I.Cantidad_Apartada-I.Cantidad_Seleccionada) > 0";
        }

        $listaLeft = '';
        $listaSelect = '';
        if ($_REQUEST['lista'] != '') {
            # code...
            $listaLeft = '';
            $listaSelect = ' 0 AS Precio_Lista ';
        }

        $query = 'SELECT
SUM(IFNULL((IC.Cantidad  ),0 )) AS cantidadContrato,
-- SUM(IFNULL((IC.Cantidad - IC.Cantidad_Apartada - IC.Cantidad_Seleccionada ),0 )) AS cantidadContrato,
GROUP_CONCAT(I.Id_Inventario_Nuevo) AS Id_Inventario_Nuevo,
I.Fecha_Vencimiento,
I.Lote,
I.Cantidad,
I.Codigo_CUM,
GE.Nombre AS Nombre_Grupo,
I.Id_Producto,
B.Id_Bodega_Nuevo,
SUM(I.Cantidad_Apartada) AS Cantidad_Apartada,
SUM(I.Cantidad_Seleccionada) AS Cantidad_Seleccionada,
/* PRD.Laboratorio_Generico, */
CONCAT(/* PRD.Principio_Activo, " ", PRD.Presentacion, " ", PRD.Concentracion, " ", */ PRD.Cantidad, " ", PRD.Unidad_Medida, " ") AS Nombre_Producto,
/* PRD.Tipo, */
PRD.Nombre_Comercial,
/* PRD.Laboratorio_Comercial, */
/* PRD.Invima, */
(SELECT CPM.Costo_Promedio FROM Costo_Promedio CPM WHERE CPM.Id_Producto = PRD.Id_Producto) AS Costo,
CONCAT( PRD.Embalaje_id,
    ". Categoria: ",
    SubC.Nombre
) AS Embalaje,
SUM(IF((I.Cantidad - I.Cantidad_Apartada - I.Cantidad_Seleccionada ) < 0, 0, (I.Cantidad - I.Cantidad_Apartada - I.Cantidad_Seleccionada)) ) AS Cantidad_Disponible,
C.Nombre AS Nombre_Categoria,


GROUP_CONCAT(CONCAT ( " Estiba ", E.Nombre , "  : ",
                        IF(
                            (
                                I.Cantidad - I.Cantidad_Apartada - I.Cantidad_Seleccionada
                            ) < 0,
                            0,
                            (
                                I.Cantidad - I.Cantidad_Apartada - I.Cantidad_Seleccionada
                            )
                         )
                    )
            ) AS "Nombre_Estiba",
' . $listaSelect . '
FROM
Inventario_Nuevo I
LEFT JOIN Inventario_Contrato IC ON I.Id_Inventario_Nuevo = IC.Id_Inventario_Nuevo
INNER JOIN Producto PRD ON
I.Id_Producto = PRD.Id_Producto
INNER JOIN Estiba E ON
E.Id_Estiba = I.Id_Estiba
INNER JOIN Grupo_Estiba GE
ON GE.Id_Grupo_Estiba = E.Id_Grupo_Estiba
INNER JOIN Bodega_Nuevo B ON
B.Id_Bodega_Nuevo = E.Id_Bodega_Nuevo
INNER JOIN Subcategoria SubC ON
PRD.Id_Subcategoria = SubC.Id_Subcategoria
INNER JOIN Categoria_Nueva C ON
SubC.Id_Categoria_Nueva = C.Id_Categoria_Nueva
' . $listaLeft;
        $query .= $condicion_principal . ' ' . $condicion . $condicion_sin_inventario . ' GROUP BY B.Id_Bodega_Nuevo, IC.Id_Inventario_Nuevo, I.Id_Producto,I.Lote,I.Fecha_Vencimiento, I.Codigo_Cum' . ' ORDER BY PRD.Nombre_Comercial LIMIT ' . $limit . ',' . $tamPag;

        // var_dump($query);exit;
        $oCon = new consulta();
        $oCon->setTipo('Multiple');
        $oCon->setQuery($query);
        $inventario['inventarios'] = $oCon->getData();
        unset($oCon);
        $i = -1;

        $inventario['numReg'] = $numReg;

        echo json_encode($inventario);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InventarioNuevo  $inventarioNuevo
     * @return \Illuminate\Http\Response
     */
    public function show(InventarioNuevo $inventarioNuevo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InventarioNuevo  $inventarioNuevo
     * @return \Illuminate\Http\Response
     */
    public function edit(InventarioNuevo $inventarioNuevo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InventarioNuevo  $inventarioNuevo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InventarioNuevo $inventarioNuevo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InventarioNuevo  $inventarioNuevo
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventarioNuevo $inventarioNuevo)
    {
        //
    }
}
