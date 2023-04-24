<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Services\consulta;
use App\Models\OrdenCompraNacional;
use Illuminate\Support\Facades\DB;

class OrdenCompraNacionalController extends Controller
{
    use ApiResponser;

    public function listarPendientes(Request $request)
    {
        $resultado = OrdenCompraNacional::with('person', 'third')
            ->whereNotIn('Estado', ['Recibida', 'Anulada'])
            ->where('Aprobacion', '=', 'Aprobada')
            ->where('Id_Bodega_Nuevo', '<>', 0)
            ->havingRaw('Id_Bodega_Nuevo')
            ->withCount(['products as Items'])
            ->when($request->codigo, function ($q, $fill) {
                $q->where('Codigo', 'like', "%$fill%");
            })
            ->when($request->proveedor, function ($q, $fill) {
                $q->whereHas('third', function ($query) use ($fill) {
                    $query->where(DB::raw('IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname))'), 'like', "%$fill%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('Codigo', 'desc')
            ->limit(20)
            ->get();
        //!Si se van a usar comoras internacionales aquí hay un ejemplo de como quedaría, no existen modelos aun
        /* $resultados = OrdenCompraInternacional::with(['funcionario', 'proveedor'])
            ->whereNotIn('Estado', ['Recibida', 'Anulada'])
            ->orderBy('Fecha_Registro', 'desc')
            ->orderBy('Codigo', 'desc')
            ->withCount(['productosOrdenCompraInternacional as Items'])
            ->get(); */

        return $this->success($resultado);
    }

    public function actaRecepcionCompra()
    {
        $codigo = (isset($_REQUEST['codigo']) ? $_REQUEST['codigo'] : '');
        $tipoCompra = (isset($_REQUEST['compra']) ? $_REQUEST['compra'] : '');
        $query = 'SELECT  COUNT(*) as Total_Items
                        FROM Producto_Orden_Compra_Nacional POCN
                        INNER JOIN Producto P ON P.Id_Producto = POCN.Id_Producto
                        INNER JOIN Orden_Compra_Nacional OCN ON OCN.Id_Orden_Compra_Nacional = POCN.Id_Orden_Compra_Nacional
                        WHERE OCN.Codigo ="' . $codigo . '"';

        $query1 = "SELECT 'Nacional' AS Tipo,
                        OCN.Id_Orden_Compra_Nacional AS Id_Orden_Compra,
                        OCN.Codigo, OCN.Identificacion_Funcionario,
                        OCN.Id_Bodega_Nuevo,
                        (SELECT P.Nombre FROM Proveedor P WHERE P.Id_Proveedor=OCN.Id_Proveedor) AS Proveedor,
                        OCN.Id_Proveedor,
                        BN.Nombre AS 'Nombre_Bodega'
                        FROM Orden_Compra_Nacional OCN
                        INNER JOIN Bodega_Nuevo BN ON BN.Id_Bodega_Nuevo = OCN.Id_Bodega_Nuevo
                        WHERE Codigo = '" . $codigo . "'";

        $query2 = ' SELECT GROUP_CONCAT(POCN.Id_Producto) as Id_Producto,
                        POCN.Id_Orden_Compra_Nacional as Id_Orden_Nacional
                        FROM Producto_Orden_Compra_Nacional POCN
                        INNER JOIN Producto P ON P.Id_Producto = POCN.Id_Producto
                        INNER JOIN Orden_Compra_Nacional OCN ON OCN.Id_Orden_Compra_Nacional = POCN.Id_Orden_Compra_Nacional
                        WHERE OCN.Codigo ="' . $codigo . '"';

        $oCon = new consulta();
        $oCon->setQuery($query);
        $res = $oCon->getData();
        unset($oCon);


        $oCon = new consulta();
        $oCon->setQuery($query1);
        $res1 = $oCon->getData();
        unset($oCon);

        $oCon = new consulta();
        $oCon->setQuery($query2);
        $id_productos = $oCon->getData();
        unset($oCon);

        $resultado['encabezado'] = $res1;
        $resultado['Items'] = $res['Total_Items'];
        $resultado['Productos'] = $id_productos;
        $query_retenciones = 'SELECT
            /* P.retefuente_type, */
            P.retefuente_account_id,
            (IF(P.retefuente_account_id IS NULL OR P.retefuente_account_id = 0, "", (SELECT Nombre FROM Plan_Cuentas WHERE Id_Plan_Cuentas = P.retefuente_account_id))) AS Nombre_Retefuente,
            (IF(P.retefuente_account_id IS NULL OR P.retefuente_account_id = 0, "0", (SELECT Porcentaje FROM Retencion WHERE Id_Plan_Cuenta = P.retefuente_account_id))) AS Porcentaje_Retefuente,
            (IF(P.retefuente_account_id IS NULL OR P.retefuente_account_id = 0, "0", (SELECT Id_Retencion FROM Retencion WHERE Id_Plan_Cuenta = P.retefuente_account_id))) AS Id_Retencion_Fte,
            P.reteica_type,
            P.reteica_account_id,
            (IF(P.reteica_account_id IS NULL OR P.reteica_account_id = 0, "", (SELECT Nombre FROM Plan_Cuentas WHERE Id_Plan_Cuentas = P.reteica_account_id))) AS Nombre_Reteica,
            (IF(P.reteica_account_id IS NULL OR P.reteica_account_id = 0, "0", (SELECT Porcentaje FROM Retencion WHERE Id_Plan_Cuenta = P.reteica_account_id))) AS Porcentaje_Reteica,
            (IF(P.retefuente_account_id IS NULL OR P.retefuente_account_id = 0, "0", (SELECT Id_Retencion FROM Retencion WHERE Id_Plan_Cuenta = P.reteica_account_id))) AS Id_Retencion_Ica,
            /* P.Contribuyente, */
            P.reteiva_account_id,
            (IF(P.reteiva_account_id IS NULL OR P.reteiva_account_id = 0, "", (SELECT Nombre FROM Plan_Cuentas WHERE Id_Plan_Cuentas = P.reteiva_account_id))) AS Nombre_Reteiva,
            (IF(P.reteiva_account_id IS NULL OR P.reteiva_account_id = 0, "0", (SELECT Porcentaje FROM Retencion WHERE Id_Plan_Cuenta = P.reteiva_account_id))) AS Porcentaje_Reteiva,
            (IF(P.retefuente_account_id IS NULL OR P.retefuente_account_id = 0, "0", (SELECT Id_Retencion FROM Retencion WHERE Id_Plan_Cuenta = P.reteiva_account_id))) AS Id_Retencion_Iva,
            regime
            FROM third_parties P
            WHERE id = "' . $res1['Id_Proveedor'] . '"';

        $oCon = new consulta();
        $oCon->setQuery($query_retenciones);
        $retenciones_proveedor = $oCon->getData();
        unset($oCon);

        $resultado['Data_Retenciones'] = $retenciones_proveedor;

        $query_configuracion = 'SELECT
            Valor_Unidad_Tributaria,
            Base_Retencion_Compras_Reg_Comun,
            Base_Retencion_Compras_Reg_Simpl,
            Base_Retencion_Compras_Ica,
            Base_Retencion_Iva_Reg_Comun
            FROM Configuracion
            WHERE Id_Configuracion = 1';

        $oCon = new consulta();
        $oCon->setQuery($query_configuracion);
        $valores_retenciones = $oCon->getData();
        unset($oCon);

        $resultado['Valores_Base_Retenciones'] = $valores_retenciones;

        return response()->json($resultado);
    }

    public function codigoBarras()
    {
        $codigo = isset($_REQUEST['codigo']) ? $_REQUEST['codigo'] : false;
        $orden = isset($_REQUEST['orden']) ? $_REQUEST['orden'] : false;
        $query = "SELECT Id_Producto FROM Producto WHERE Codigo_Barras LIKE '$codigo%'";
        $oCon = new consulta();
        $oCon->setQuery($query);
        $resultado = $oCon->getData();
        unset($oCon);
        $query = 'SELECT Id_Orden_Compra_Nacional, Codigo FROM Orden_Compra_Nacional WHERE Codigo="' . $orden . '"';
        $oCon = new consulta();
        $oCon->setQuery($query);
        $orden = $oCon->getData();
        unset($oCon);
        $cum = explode("-", $resultado['Id_Producto']);
        $condicion = '';
        if ($orden['Id_Orden_Compra_Nacional']) {
            $condicion .= "  AND POC.Id_Orden_Compra_Nacional=" . $orden['Id_Orden_Compra_Nacional'];
        }
        if ($resultado['Id_Producto'] && $orden['Id_Orden_Compra_Nacional']) {
            $query = ' SELECT IFNULL(CONCAT(P.Presentacion," ", P.Cantidad," ", P.Unidad_Medida),
                CONCAT(P.Nombre_Comercial)) as Nombre_Producto,
                P.Imagen AS Foto,
                "No" as Eliminado,
                P.Id_Categoria,
                P.Id_Subcategoria,
                P.Id_Producto,
                P.Nombre_Comercial,
                IFNULL((SELECT POC.Id_Producto_Orden_Compra_Nacional
                    FROM Producto_Orden_Compra_Nacional POC
                    INNER JOIN Producto P ON POC.Id_Producto=P.Id_Producto
                    WHERE P.Id_Producto LIKE "%' . $cum[0] . '%" ' . $condicion . ' LIMIT 1 ),0) as Id_Producto_Orden_Compra,
                IFNULL((SELECT SUM(POC.Cantidad) FROM Producto_Orden_Compra_Nacional POC WHERE POC.Id_Producto=' . $resultado['Id_Producto'] . $condicion . ' GROUP BY POC.Id_Producto ),0) as CantidadProducto,
                IFNULL((SELECT POC.Total FROM Producto_Orden_Compra_Nacional POC WHERE POC.Id_Producto=' . $resultado['Id_Producto'] . $condicion . ' GROUP BY POC.Id_Producto ),0) as CostoProducto,
                P.Embalaje_id,
                IF(P.impuesto_id=2,19,0) AS Impuesto
                FROM Producto P
                WHERE P.Id_Producto=' . $resultado['Id_Producto'];
        }

        $oCon = new consulta();
        $oCon->setQuery($query);
        $resultado = $oCon->getData();
        unset($oCon);

        if ($resultado['Id_Producto'] != $resultado['Id_Producto_Orden_Compra'] && $resultado['Id_Producto_Orden_Compra'] != 0) {
            $resultado['Diferente'] = true;
        }
        $prod[0]['Precio'] = '0';
        $prod[0]['Cantidad'] = '0';
        $prod[0]['Fecha_Vencimiento'] = '';
        $prod[0]['Impuesto'] = $resultado['Impuesto'];
        $prod[0]['Subtotal'] = 0;
        $prod[0]['Lote'] = '';
        $prod[0]['Factura'] = '';
        $prod[0]['Id_Producto'] = $resultado['Id_Producto'];
        $prod[0]['Id_Producto_Orden_Compra'] = $resultado['Id_Producto_Orden_Compra'];
        $prod[0]['Codigo_Compra'] = $orden['Codigo'];
        $prod[0]['Required'] = true;
        $prod[0]['No_Conforme'] = 0;
        $resultado['producto'] = $prod;




        echo json_encode($resultado);
    }
}
