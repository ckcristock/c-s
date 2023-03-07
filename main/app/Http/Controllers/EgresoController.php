<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\complex;

include(app_path() . '/Http/Services/comprobantes/ObtenerProximoConsecutivo.php');
include(app_path() . '/Http/Services/resumenretenciones/funciones.php');

class EgresoController extends Controller
{
    function generarQr($id_remision)
    {
        /* AQUI GENERA QR */
        //$qr = generarqr('remision', $id_remision, '/IMAGENES/QR/');
        $oItem = new complex("Remision", "Id_Remision", $id_remision);
        //$oItem->Codigo_Qr = $qr;
        $oItem->save();
        unset($oItem);

        return;
    }
    public function guardar()
    {
        $datos = isset($_REQUEST['Datos']) ? $_REQUEST['Datos'] : false;
        $cuentas_contables = isset($_REQUEST['Cuentas_Contables']) ? $_REQUEST['Cuentas_Contables'] : false;

        $datos = json_decode($datos, true);
        $cuentas_contables = json_decode($cuentas_contables, true);

        /* var_dump($datos);
var_dump($cuentas_contables);
exit;
 */

        $mes = isset($datos['Fecha_Documento']) ? date('m', strtotime($datos['Fecha_Documento'])) : date('m');
        $anio = isset($datos['Fecha_Documento']) ? date('Y', strtotime($datos['Fecha_Documento'])) : date('Y');

        $cod = generarConsecutivo('Egreso', $mes, $anio);
        $datos['Codigo'] = $cod;

        $oItem = new complex("Documento_Contable", "Id_Documento_Contable");

        if (!isset($datos['Id_Centro_Costo']) || $datos['Id_Centro_Costo'] == '') {
            $datos['Id_Centro_Costo'] = 0;
        }

        foreach ($datos as $index => $value) {
            $oItem->$index = $value;
        }
        $oItem->Tipo = 'Egreso';

        $oItem->save();
        $id_documento_contable = $oItem->getId();
        unset($oItem);

        /* AQUI GENERA QR */
        //$qr = generarqr('egreso', $id_documento_contable, '/IMAGENES/QR/');
        $oItem = new complex("Documento_Contable", "Id_Documento_Contable", $id_documento_contable);
        //$oItem->Codigo_Qr = $qr;
        $oItem->save();
        unset($oItem);
        /* HASTA AQUI GENERA QR */

        unset($cuentas_contables[count($cuentas_contables) - 1]);

        foreach ($cuentas_contables as $cuenta) {
            $oItem = new complex('Cuenta_Documento_Contable', 'Id_Cuenta_Documento_Contable');
            $oItem->Id_Documento_Contable = $id_documento_contable;
            $oItem->Id_Plan_Cuenta = $cuenta['Id_Plan_Cuentas'];
            if ($datos['Forma_Pago'] == 'Cheque' && $cuenta['Credito'] > 0) {
                $response_cheque = generarConsecutivoCheque($cuenta['Id_Plan_Cuentas']);
                if ($response_cheque['status'] == 2) {
                    $oItem->Cheque = $response_cheque['consecutivo'];
                }
            }
            $oItem->Nit = $cuenta['Nit_Cuenta'];
            $oItem->Tipo_Nit = $cuenta['Tipo_Nit'];
            $oItem->Id_Centro_Costo = isset($cuenta['Id_Centro_Costo']) && $cuenta['Id_Centro_Costo'] != '' ? $cuenta['Id_Centro_Costo'] : '0';
            $oItem->Id_Empresa = $cuenta['Id_Empresa'];
            $oItem->Documento = $cuenta['Documento'];
            $oItem->Concepto = $cuenta['Concepto'];
            $oItem->Base = number_format($cuenta['Base'], 2, ".", "");
            $oItem->Debito = number_format($cuenta['Debito'], 2, ".", "");
            $oItem->Credito = number_format($cuenta['Credito'], 2, ".", "");
            $oItem->Deb_Niif = number_format($cuenta['Deb_Niif'], 2, ".", "");
            $oItem->Cred_Niif = number_format($cuenta['Cred_Niif'], 2, ".", "");
            $oItem->save();
            unset($oItem);

            // cambiarEstadoFactura($cuenta);

            ## REGISTRAR MOVIMIENTO CONTABLE...
            $oItem = new complex("Movimiento_Contable", "Id_Movimiento_Contable");
            $oItem->Id_Plan_Cuenta = $cuenta['Id_Plan_Cuentas'];
            $oItem->Id_Modulo = 7;
            $oItem->Id_Registro_Modulo = $id_documento_contable;
            $oItem->Fecha_Movimiento = $datos['Fecha_Documento'];
            $oItem->Debe = number_format($cuenta['Debito'], 2, ".", "");
            $oItem->Debe_Niif = number_format($cuenta['Deb_Niif'], 2, ".", "");
            $oItem->Haber = number_format($cuenta['Credito'], 2, ".", "");
            $oItem->Haber_Niif = number_format($cuenta['Cred_Niif'], 2, ".", "");
            $oItem->Nit = $cuenta['Nit_Cuenta'];
            $oItem->Tipo_Nit = $cuenta['Tipo_Nit'];
            $oItem->Id_Centro_Costo = isset($cuenta['Id_Centro_Costo']) && $cuenta['Id_Centro_Costo'] != '' ? $cuenta['Id_Centro_Costo'] : '0';
            $oItem->Documento = $cuenta['Documento'];
            $oItem->Numero_Comprobante = $cod;
            $oItem->Detalles = $cuenta['Concepto'];
            $oItem->save();
            unset($oItem);

            cambiarEstadoFactura($cuenta['Nit_Cuenta'], $cuenta['Documento'], $cuenta['Id_Plan_Cuentas']); // Metodo que cambia el estado de la factura a "pagada"
        }

        if (isset($datos['Id_Borrador']) && $datos['Id_Borrador'] != '') {
            eliminarBorradorContable($datos['Id_Borrador']);
        }

        if ($id_documento_contable) {
            $resultado['mensaje'] = "Se ha registrado un comprobante de egreso satisfactoriamente";
            $resultado['tipo'] = "success";
            $resultado['titulo'] = "Operación Exitosa!";
            $resultado['id'] = $id_documento_contable;
        } else {
            $resultado['mensaje'] = "Ha ocurrido un error de conexión, comunicarse con el soporte técnico.";
            $resultado['tipo'] = "error";
        }

        echo json_encode($resultado);

        /* function cambiarEstadoFactura($factura) {
    if (isset($factura['Valor_Factura']) && isset($factura['Valor_Abono'])) {
        $valor_factura = number_format($factura['Valor_Factura'],2,".","");
        $valor_abono = number_format($factura['Valor_Abono'],2,".","");
        $por_pagar = $valor_factura - $valor_abono;

        if (($por_pagar == $factura['Debito']) || ($por_pagar == $factura['Credito'])) { // Valida si lo que falta por pagar es igual a lo que viene en el debito o el credito

            $query = "UPDATE Factura_Acta_Recepcion SET Estado = 'Pagada' WHERE Factura = '$factura[Documento]' AND Id_Acta_Recepcion = $factura[Id_Factura]";

            $oCon = new consulta();
            $oCon->setQuery($query);
            $oCon->createData();
            unset($oCon);

            $query = "UPDATE Facturas_Proveedor_Mantis SET Estado = 'Pagada' WHERE Factura = '$factura[Documento]' AND Id_Facturas_Proveedor_Mantis = $factura[Id_Factura]";

            $oCon = new consulta();
            $oCon->setQuery($query);
            $oCon->createData();
            unset($oCon);
        }
    }
} */
    }

    public function listaFacturas()
    {
        $id = isset($_REQUEST['nit']) ? $_REQUEST['nit'] : false;
        $fecha = isset($_REQUEST['fecha']) ? $_REQUEST['fecha'] : false;
        $id_plan_cuenta = isset($_REQUEST['id_plan_cuenta']) ? $_REQUEST['id_plan_cuenta'] : false;

        $resultado['Facturas'] = listaCartera($id, $id_plan_cuenta, $fecha);


        return json_encode($resultado);
    }
}
