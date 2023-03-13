<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use App\Http\Services\consulta;
use App\Http\Services\complex;

class FacturaController extends Controller
{
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
     * @param  \App\Models\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function show(Factura $factura)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function edit(Factura $factura)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Factura $factura)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function destroy(Factura $factura)
    {
        //
    }

    public function getNotasCreditos()
    {
        $condicion = '';
        if (isset($_REQUEST['cod_nota'])) {
            $condicion .= ' WHERE Codigo_Nota like "%' . $_REQUEST['cod_nota'] . '%"';
        }
        if (isset($_REQUEST['fecha_nota']) && $_REQUEST['fecha_nota'] != "") {
            $fecha_inicio = trim(explode(' - ', $_REQUEST['fecha_nota'])[0]);
            $fecha_fin = trim(explode(' - ', $_REQUEST['fecha_nota'])[1]);
            if ($condicion) {
                $condicion .= "AND Fecha_Nota BETWEEN '$fecha_inicio' AND '$fecha_fin'";
            } else {
                $condicion .= "WHERE DATE(Fecha_Nota) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
            }
        }
        if (isset($_REQUEST['cliente'])) {
            if ($condicion) {
                $condicion .= 'AND Cliente LIKE "%' . $_REQUEST['cliente'] . '%" ';
            } else {
                $condicion .= 'WHERE Cliente LIKE "%' . $_REQUEST['cliente'] . '%" ';
            }
        }
        if (isset($_REQUEST['funcionario'])) {
            if ($condicion) {
                $condicion .= 'AND Funcionario LIKE "%' . $_REQUEST['funcionario'] . '%" ';
            } else {
                $condicion .= ' WHERE Funcionario LIKE "%' . $_REQUEST['funcionario'] . '%" ';
            }
        }
        if (isset($_REQUEST['cod_factura'])) {
            if ($condicion) {
                $condicion .= 'AND Codigo_Factura LIKE "%' . $_REQUEST['cod_factura'] . '%" ';
            } else {
                $condicion .= ' WHERE Codigo_Factura LIKE "%' . $_REQUEST['cod_factura'] . '%" ';
            }
        }
        /*
        if (isset($_REQUEST['estado_fact'])) {
            if ($condicion) {
                $condicion .= 'AND Estado_Factura LIKE "%'.$_REQUEST['estado_fact'].'%" ';
            }else{
                $condicion .=' WHERE Estado_Factura LIKE "%'.$_REQUEST['estado_fact'].'%" ';
            }

        } */
        $query =
            'SELECT COUNT(*) AS Total
            FROM ( SELECT F.Codigo AS Codigo_Factura,
                F.Id_Factura_Administrativa AS Id_Factura,
                "Factura_Administrativa" AS Tipo_Factura,
                NT.Codigo AS Codigo_Nota,
                NT.Fecha AS Fecha_Nota,
                NT.Id_Nota_Credito_Global,
                F.Cliente ,
                F.Funcionario
                FROM( SELECT  FA.* ,
                    IFNULL(CONCAT(F.first_name," ",F.first_surname),F.first_name) AS Cliente,
                    IFNULL(CONCAT(F.first_name," ",F.first_surname),F.first_name) AS Funcionario
                    FROM Factura_Administrativa FA
                    INNER JOIN people F ON F.id = FA.Identificacion_Funcionario
                    WHERE Tipo_Cliente = "Funcionario"
                    UNION ALL
                    SELECT  FA.* , C.Nombre AS Cliente,
                        IFNULL(CONCAT(F.first_name," ",F.first_surname),F.first_name) AS Funcionario
                        FROM Factura_Administrativa FA
                        INNER JOIN Cliente C ON C.Id_Cliente = FA.Id_Cliente
                        INNER JOIN people F ON F.id = FA.Identificacion_Funcionario
                        WHERE Tipo_Cliente = "Cliente"
                        UNION ALL
                        SELECT  FA.* , P.Nombre AS Cliente,
                        IFNULL(CONCAT(F.first_name," ",F.first_surname),F.first_name) AS Funcionario
                        FROM Factura_Administrativa FA
                        INNER JOIN Proveedor P ON P.Id_Proveedor = FA.Id_Cliente
                        INNER JOIN people F ON F.id = FA.Identificacion_Funcionario
                        WHERE Tipo_Cliente = "Proveedor"
                ) AS F
                INNER JOIN Nota_Credito_Global NT
                    ON NT.Id_Factura = F.Id_Factura_Administrativa AND NT.Tipo_Factura = "Factura_Administrativa"
                UNION
                SELECT F.Codigo AS Codigo_Factura,
                    F.Id_Factura_Venta AS Id_Factura,
                    "Factura_Venta" AS Tipo_Factura,
                    NT.Codigo AS Codigo_Nota,
                    NT.Fecha AS Fecha_Nota,
                    NT.Id_Nota_Credito_Global,
                    C.Nombre AS Cliente,
                    IFNULL(CONCAT(FU.first_name," ",FU.first_surname),FU.first_name) AS Funcionario
                    FROM Factura_Venta F
                INNER JOIN Nota_Credito_Global NT ON NT.Id_Factura = F.Id_Factura_Venta AND NT.Tipo_Factura = "Factura_Venta"
                INNER JOIN Cliente C ON C.Id_Cliente = F.Id_Cliente
                INNER JOIN people FU ON FU.id = NT.Id_Funcionario
                UNION ALL
                SELECT F.Codigo AS Codigo_Factura,
                    F.Id_Factura AS Id_Factura,
                    "Factura" AS Tipo_Factura,
                    NT.Codigo AS Codigo_Nota,
                    NT.Fecha AS Fecha_Nota,
                    NT.Id_Nota_Credito_Global,
                    C.Nombre AS Cliente,
                    IFNULL(CONCAT(FU.first_name," ",FU.first_surname),FU.first_name) AS Funcionario
                    FROM Factura F
                INNER JOIN Nota_Credito_Global NT ON NT.Id_Factura = F.Id_Factura AND NT.Tipo_Factura = "Factura"
                INNER JOIN Cliente C ON C.Id_Cliente = F.Id_Cliente
                INNER JOIN people FU ON FU.id = NT.Id_Funcionario
                UNION ALL
                SELECT F.Codigo AS Codigo_Factura,
                    F.Id_Factura_Capita AS Id_Factura_Capita,
                    "Factura_Capita" AS Tipo_Factura,
                    NT.Codigo AS Codigo_Nota,
                    NT.Fecha AS Fecha_Nota,
                    NT.Id_Nota_Credito_Global,
                    C.Nombre AS Cliente,
                    IFNULL(CONCAT(FU.first_name," ",FU.first_surname),FU.first_name) AS Funcionario
                    FROM Factura_Capita F
                INNER JOIN Nota_Credito_Global NT ON NT.Id_Factura = F.Id_Factura_Capita AND NT.Tipo_Factura = "Factura_Capita"
                INNER JOIN Cliente C ON C.Id_Cliente = F.Id_Cliente
                INNER JOIN people FU ON FU.id = NT.Id_Funcionario
            ) AS Notas' . $condicion;
        $oCon = new consulta();
        $oCon->setQuery($query);
        $numReg = $oCon->getData();
        unset($oCon);
        $currentPage = '';
        $numReg = $numReg['Total'];
        $perPage = 15;
        $from = "";
        $to = "";
        if (isset($_REQUEST['pag'])) {
            $currentPage = $_REQUEST['pag'];
            $from = ($currentPage - 1) * $perPage;
        } else {
            $currentPage = 1;
            $from = 0;
        }
        $query = ' SELECT * FROM ( SELECT
        F.Codigo AS Codigo_Factura, F.Id_Factura_Administrativa AS Id_Factura, "Factura_Administrativa" AS Tipo_Factura, NT.Codigo AS Codigo_Nota, NT.Fecha AS Fecha_Nota, NT.Id_Nota_Credito_Global,  F.Cliente , F.Funcionario
        FROM(
            SELECT  FA.* , IFNULL(CONCAT(F.first_name," ",F.first_surname),F.first_name) AS Cliente,
          IFNULL(CONCAT(F.first_name," ",F.first_surname),F.first_name) AS Funcionario
            FROM Factura_Administrativa FA
            INNER JOIN people F ON F.id = FA.Identificacion_Funcionario
            WHERE Tipo_Cliente = "Funcionario"

            UNION ALL

            SELECT  FA.* , C.Nombre AS Cliente,
            IFNULL(CONCAT(F.first_name," ",F.first_surname),F.first_name) AS Funcionario
            FROM Factura_Administrativa FA
            INNER JOIN Cliente C ON C.Id_Cliente = FA.Id_Cliente
            INNER JOIN people F ON F.id = FA.Identificacion_Funcionario
            WHERE Tipo_Cliente = "Cliente"

            UNION ALL

            SELECT  FA.* , P.Nombre AS Cliente,
            IFNULL(CONCAT(F.first_name," ",F.first_surname),F.first_name) AS Funcionario
            FROM Factura_Administrativa FA
            INNER JOIN Proveedor P ON P.Id_Proveedor = FA.Id_Cliente
            INNER JOIN people F ON F.id = FA.Identificacion_Funcionario
            WHERE Tipo_Cliente = "Proveedor"
            ) AS F
            INNER JOIN Nota_Credito_Global NT ON NT.Id_Factura = F.Id_Factura_Administrativa AND NT.Tipo_Factura = "Factura_Administrativa"

         UNION
         SELECT F.Codigo AS Codigo_Factura, F.Id_Factura_Venta AS Id_Factura, "Factura_Venta" AS Tipo_Factura, NT.Codigo AS Codigo_Nota, NT.Fecha AS Fecha_Nota, NT.Id_Nota_Credito_Global,  C.Nombre AS Cliente , IFNULL(CONCAT(FU.first_name," ",FU.first_surname),FU.first_name) AS Funcionario

        FROM Factura_Venta F
        INNER JOIN Nota_Credito_Global NT ON NT.Id_Factura = F.Id_Factura_Venta AND NT.Tipo_Factura = "Factura_Venta"
        INNER JOIN Cliente C ON C.Id_Cliente = F.Id_Cliente
        INNER JOIN people FU ON FU.id = NT.Id_Funcionario
        UNION ALL
        SELECT F.Codigo AS Codigo_Factura, F.Id_Factura AS Id_Factura, "Factura" AS Tipo_Factura, NT.Codigo AS Codigo_Nota, NT.Fecha AS Fecha_Nota, NT.Id_Nota_Credito_Global,  C.Nombre AS Cliente , IFNULL(CONCAT(FU.first_name," ",FU.first_surname),FU.first_name) AS Funcionario
         FROM Factura F
        INNER JOIN Nota_Credito_Global NT ON NT.Id_Factura = F.Id_Factura AND NT.Tipo_Factura = "Factura"
        INNER JOIN Cliente C ON C.Id_Cliente = F.Id_Cliente
        INNER JOIN people FU ON FU.id = NT.Id_Funcionario
        UNION ALL
        SELECT
        F.Codigo AS Codigo_Factura, F.Id_Factura_Capita AS Id_Factura_Capita, "Factura_Capita" AS Tipo_Factura, NT.Codigo AS Codigo_Nota, NT.Fecha AS Fecha_Nota, NT.Id_Nota_Credito_Global,  C.Nombre AS Cliente , IFNULL(CONCAT(FU.first_name," ",FU.first_surname),FU.first_name) AS Funcionario

        FROM Factura_Capita F
        INNER JOIN Nota_Credito_Global NT ON NT.Id_Factura = F.Id_Factura_Capita AND NT.Tipo_Factura = "Factura_Capita"
        INNER JOIN Cliente C ON C.Id_Cliente = F.Id_Cliente
        INNER JOIN people FU ON FU.id = NT.Id_Funcionario
        ) AS Notas
        ' . $condicion . '  ORDER BY Notas.Fecha_Nota DESC
        LIMIT ' . $from . ' , ' . $perPage . '

        ';

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $facturas = $oCon->getData();
        unset($oCon);


        $response['Notas_Credito'] = $facturas;
        $response['numReg'] = $numReg;


        return json_encode($response);
    }

    public function listaFacturaClienteNotasCredito()
    {
        $id = (isset($_REQUEST['id']) ? $_REQUEST['id'] : '');
        $modelo = (isset($_REQUEST['modelo']) ? $_REQUEST['modelo'] : '');
        $codigo = (isset($_REQUEST['codigo']) ? $_REQUEST['codigo'] : '');
        $tipoCliente = (isset($_REQUEST['tipoCliente']) ? $_REQUEST['tipoCliente'] : '');

        $joins = $this->joins_db($modelo);
        $where = $this->condicion_db($modelo, $id, $codigo, $tipoCliente);
        $selects = $this->selects_db($modelo);
        $query = $selects . 'FROM  ' . $modelo . ' F' . $joins . $where;
        $oCon = new consulta();
        $oCon->setQuery($query);
        $factura = $oCon->getData();
        unset($oCon);
        if ($factura) {
            $valor_nota = $this->factura_nota_credito($factura['Id_Factura'], $modelo);
            $valor_factura = $this->select_db_productos($factura['Id_Factura'], $modelo);
            if ($valor_factura > $valor_nota) {
                $resultado['tipo'] = 'success';
                $resultado['Factura'] = $factura;
            } else {
                $resultado['tipo'] = 'error';
                $resultado['title'] = 'Factura con nota crédito';
                $resultado['mensaje'] = 'A esta factura ya se le realizó una nota por el valor total de la factura';
            }
        } else {
            $resultado['tipo'] = 'error';
            $resultado['title'] = 'Factura no encotrada';
            $resultado['mensaje'] = 'No se ha encontrada factura asociada a ese código';
        }
        return json_encode($resultado);
    }

    function selects_db($modelo)
    {
        $selects = 'SELECT
        F.Id_' . $modelo . '  AS Id_Factura, F.Codigo as Codigo, F.Nota_Credito ';

        if ($modelo == 'Factura') {
            $selects .= ' , F.Id_Dispensacion';
        }
        return $selects;
    }
    function joins_db($modelo)
    {
        $joins = '';
        if ($modelo == 'Factura_Capita' || $modelo == 'Factura_Administrativa') {
            $joins .= '
              INNER JOIN  Descripcion_' . $modelo . ' PF
              ON PF.Id_' . $modelo . '=F.Id_' . $modelo . ' ';
        } else {
            $joins .= '
              INNER JOIN Producto_' . $modelo . ' PF
              ON PF.Id_' . $modelo . ' = F.Id_' . $modelo;
        }
        return $joins;
    }
    function condicion_db($modelo, $id, $codigo, $tipoCliente)
    {
        $condicion = '
        WHERE F.Id_Cliente=' . $id;

        if ($modelo == 'Factura_Venta') {
            $condicion .= ' AND F.Estado <> "Anulada" AND F.Estado <> "Pagada" ';
        } else {
            $condicion .= ' AND F.Estado_Factura <> "Anulada" AND F.Estado_Factura <> "Pagada" ';
        }
        if ($modelo == 'Factura_Administrativa') {
            $condicion .= 'AND F.Tipo_Cliente ="' . $tipoCliente . '" ';
        }
        $condicion .= ' AND F.Codigo = "' . $codigo . '"';
        return $condicion;
    }
    function factura_nota_credito($id_factura, $modelo)
    {
        $query = 'SELECT Id_Nota_Credito_Global, Codigo FROM Nota_Credito_Global WHERE Tipo_Factura = "' . $modelo . '" AND Id_Factura = ' . $id_factura;
        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $notas_creditos = $oCon->getData();
        unset($oCon);
        $total_de_notas = 0;
        if ($notas_creditos) {
            # code...
            foreach ($notas_creditos as $nota) {
                # code...

                $query = 'SELECT SUM(Valor_Nota_Credito) AS Total_Nota
                    FROM Producto_Nota_Credito_Global
                    WHERE Id_Nota_Credito_Global = ' . $nota['Id_Nota_Credito_Global'] . '
                    GROUP BY Id_Nota_Credito_Global';;
                $oCon = new consulta();

                $oCon->setQuery($query);
                $valor = $oCon->getData();
                $total_de_notas += $valor['Total_Nota'];
            }
        }
        return $total_de_notas;
    }

    function select_db_productos($id_factura, $modelo)
    {
        $modelo_producto = '';

        if ($modelo == 'Factura_Capita' || $modelo == 'Factura_Administrativa') {
            $modelo_producto = 'Descripcion_' . $modelo;
        } else {
            $modelo_producto = 'Producto_' . $modelo;
        }

        //GENERALES
        $query = 'SELECT  PF.Cantidad,  PF.Descuento, PF.Impuesto,';

        //productos y ids modelo producto


        // seleccionar precio
        if ($modelo_producto == 'Producto_Factura_Venta') {
            # code...
            $query .= 'PF.Precio_Venta AS Precio ';
        } else {
            $query .= 'PF.Precio';
        }
        $query .= ' FROM ' . $modelo_producto . ' PF WHERE Id_' . $modelo . '=' . $id_factura;


        $oCon = new consulta();

        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $productos = $oCon->getData();

        $acumulador = 0;
        foreach ($productos as $producto) {
            $acumulador += $this->calcularSubtotal($producto);
        }
        /*  var_dump($acumulador); */
        return $acumulador;
    }

    function calcularSubtotal($Item)
    {
        $valor_iva = ((float)($Item->Impuesto) / 100) *
            (((float)($Item->Cantidad) *
                (float)($Item->Precio)) -
                ((float)($Item->Cantidad) *
                    (float)($Item->Descuento))
            );
        $subtotal = ((float)($Item->Cantidad) * (float)($Item->Precio)) - ((float)($Item->Cantidad) * (float)($Item->Descuento));
        $resultado = $subtotal + $valor_iva;

        return $resultado;
    }

    function validarExistenciaNotaGlobal($id, $response)
    {
        $oItem = new complex('Factura_Venta', 'Id_Factura_Venta', $id);
        $factura = $oItem->getData();
        unset($oItem);

        $query = ' SELECT GROUP_CONCAT( Codigo )   AS Codigos
                    FROM Nota_Credito_Global
                   WHERE Codigo_Factura = "' . $factura['Codigo'] . '"
                   GROUP BY Codigo_Factura';
        $oCon = new consulta();
        $oCon->setQuery($query);
        $notas_globales = $oCon->getData();

        if (!$notas_globales) {
            # code...
            return false;
        }
        $response['type'] = 'error';
        $response['title'] = 'OOPS! Existe Nota Crédito creada para esta factura';
        $response['message'] = 'Se ha realizado nota credito tipo precio (NO AFECTA INVENTARIO) con anterioridad : ' . $notas_globales['Codigos'];

        return true;
    }
    function GetProductosNotaCreditoFactura($idFactura)
    {
        $query = '
                  SELECT
                      IFNULL(GROUP_CONCAT(Id_Producto), 0) AS Excluir_Productos
                  FROM Nota_Credito NC
                  INNER JOIN Producto_Nota_Credito PNC ON NC.Id_Nota_Credito = PNC.Id_Nota_Credito
                  WHERE
                      NC.Id_Factura = ' . $idFactura . ' AND NC.Estado!="Anulada" ';

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('simple');
        $productos_nota_credito = $oCon->getData();
        unset($oCon);

        return $productos_nota_credito['Excluir_Productos'];
    }

    public function listaProductoNotasCredito()
    {
        $id = (isset($_REQUEST['id']) ? $_REQUEST['id'] : '');

        $response = [];
        if ($id == '') {
            echo json_encode(array());
            return;
        }

        if (!$this->validarExistenciaNotaGlobal($id, $response)) {


            $productos_excluir = $this->GetProductosNotaCreditoFactura($id);

            $condicion_productos_excluir = ' HAVING Cantidad > 0';


            $query2 = 'SELECT PFV.*,
                IFNULL(CONCAT(P.Nombre_Comercial, " - ",P.Principio_Activo, " ", P.Cantidad,"", P.Unidad_Medida, " " , P.Presentacion, "\n", P.Invima, " CUM:", P.Codigo_Cum),
                CONCAT(P.Nombre_Comercial, " LAB-", P.Laboratorio_Comercial)) as producto,
                P.Id_Producto,
                IF(P.Laboratorio_Generico IS NULL,P.Laboratorio_Comercial,P.Laboratorio_Generico) as Laboratorio,
                P.Presentacion,
                P.Codigo_Cum as Cum,
                PFV.Fecha_Vencimiento as Vencimiento,
                PFV.Lote as Lote,
                "true" as Disabled, 0 as Subtotal_Nota, 0 as Iva,
                (PFV.Cantidad - (SELECT IFNULL(SUM(PNC.Cantidad), 0) FROM Producto_Nota_Credito PNC INNER JOIN Nota_Credito NC ON PNC.Id_Nota_Credito = NC.Id_Nota_Credito WHERE NC.Id_Factura = PFV.Id_Factura_Venta AND PNC.Id_Producto = PFV.Id_Producto AND PNC.Lote = PFV.Lote AND NC.Estado!="Anulada")) AS Cantidad
                FROM Producto_Factura_Venta PFV

                LEFT JOIN Producto P ON P.Id_Producto = PFV.Id_Producto
                /* WHERE PFV.Id_Factura_Venta = */' /* . $id . $condicion_productos_excluir */;

            $oCon = new consulta();
            $oCon->setQuery($query2);
            $oCon->setTipo('Multiple');
            $productos = $oCon->getData();
            unset($oCon);

            if (count($productos) == 0) {
                $query22 = 'SELECT PFV.*,
        IFNULL(CONCAT(P.Principio_Activo, " ", P.Cantidad,"", P.Unidad_Medida, " " , P.Presentacion, "\n", P.Invima, " CUM:", P.Codigo_Cum), CONCAT(P.Nombre_Comercial, " LAB-", P.Laboratorio_Comercial)) as producto,
        P.Id_Producto,
        IF(P.Laboratorio_Generico IS NULL,P.Laboratorio_Comercial,P.Laboratorio_Generico) as Laboratorio,
        P.Presentacion,
        P.Codigo_Cum as Cum,
        PFV.Fecha_Vencimiento as Vencimiento,
        PFV.Lote as Lote,
        PFV.Id_Inventario_Nuevo as Id_Inventario_Nuevo,
        PFV.Precio_Venta as Costo_unitario,
        PFV.Cantidad as Cantidad,
        PFV.Precio_Venta as PrecioVenta,
        PFV.Subtotal as Subtotal,
        PFV.Id_Producto_Factura_Venta as idPFV,"true" as Disabled, 0 as Subtotal_Nota, 0 as Iva,
        (PFV.Cantidad - (SELECT IFNULL(SUM(PNC.Cantidad), 0) FROM Producto_Nota_Credito PNC INNER JOIN Nota_Credito NC ON PNC.Id_Nota_Credito = NC.Id_Nota_Credito WHERE NC.Id_Factura = PFV.Id_Factura_Venta AND PNC.Id_Producto = PFV.Id_Producto AND PNC.Lote = PFV.Lote)) AS Cantidad
        FROM Producto_Factura_Venta PFV
        LEFT JOIN Producto P ON PFV.Id_Producto = P.Id_Producto
        WHERE PFV.Id_Factura_Venta =' . $id . $condicion_productos_excluir;

                $oCon = new consulta();
                $oCon->setQuery($query22);
                $oCon->setTipo('Multiple');
                $productos = $oCon->getData();
                unset($oCon);
            }

            $response['type'] = 'success';
            $response['data'] = $productos;
        }

        return json_encode($response);
    }

    public function guardarNotaCredito()
    {
        $mod = (isset($_REQUEST['modulo']) ? $_REQUEST['modulo'] : '');
        $datos = (isset($_REQUEST['datos']) ? $_REQUEST['datos'] : '');
        $productos = (isset($_REQUEST['productos']) ? $_REQUEST['productos'] : '');

        $datos = (array) json_decode($datos);
        $productos = (array) json_decode($productos, true);



        //$cod= $configuracion->getConsecutivo('Nota_Credito','Nota_Credito');

        $cod = '45645646456456'; //generarConsecutivo()

        // $query = 'SELECT E.Id_Bodega_Nuevo
        //     FROM Inventario_Nuevo I
        //     INNER JOIN Estiba E ON E.Id_Estiba = I.Id_Estiba
        //     WHERE I.Id_Inventario_Nuevo = ' . $productos[0]['Id_Inventario_Nuevo'];
        // $oCon = new consulta();
        // $oCon->setQuery($query);
        // $id_bodega_nuevo = $oCon->getData();
        // $id_bodega_nuevo = $id_bodega_nuevo['Id_Bodega_Nuevo'];


        $datos['Codigo'] = $cod;

        $oItem = new complex($mod, "Id_" . $mod);
        foreach ($datos as $index => $value) {
            $oItem->$index = $value;
        }
        $oItem->Id_Bodega_Nuevo = 0;
        $oItem->save();
        $id_venta = $oItem->getId();
        $resultado = array();
        unset($oItem);

        /* AQUI GENERA QR */
        //$qr = generarqr('notascredito', $id_venta, '/IMAGENES/QR/');
        $oItem = new complex("Nota_Credito", "Id_Nota_Credito", $id_venta);
        //$oItem->Codigo_Qr = $qr;
        $oItem->save();
        unset($oItem);
        /* HASTA AQUI GENERA QR */

        foreach ($productos as $producto) {
            if ($producto['Nota']) {
                $oItem = new complex('Producto_Nota_Credito', "Id_Producto_Nota_Credito");
                $producto["Id_Nota_Credito"] = $id_venta;
                foreach ($producto as $index => $value) {
                    $oItem->$index = $value;
                }
                $oItem->Id_Inventario = 0;
                $oItem->Id_Inventario_Nuevo = 0;
                $subtotal = $producto['Cantidad_Ingresada'] * $producto['Precio_Venta'];
                $oItem->Cantidad = $producto['Cantidad_Ingresada'];
                $oItem->Subtotal = number_format($subtotal, 2, ".", "");
                $oItem->Id_Motivo = $producto['Id_Motivo'];
                $oItem->save();
                unset($oItem);
            }
        }
        if ($id_venta != "") {
            $resultado['mensaje'] = "Se ha guardado correctamente la nota credito con codigo: " . $datos['Codigo'];
            $resultado['tipo'] = "success";
        } else {
            $resultado['mensaje'] = "ha ocurrido un error guardando la informacion, por favor verifique";
            $resultado['tipo'] = "error";
        }

        return json_encode($resultado);
    }
}
