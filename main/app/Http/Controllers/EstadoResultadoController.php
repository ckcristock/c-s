<?php

namespace App\Http\Controllers;

use App\Exports\EstadoResultadoExport;
use Illuminate\Http\Request;
use App\Http\Services\consulta;
use App\Http\Services\complex;
use App\Models\Company;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;

include(app_path() . '/Http/Services/estadoresultado/querys.php');

class EstadoResultadoController extends Controller
{

    function fecha($fecha)
    {
        return date('d/m/Y', strtotime($fecha));
    }

    function getCampo()
    {
        $campo['codigo'] = $_REQUEST['Tipo'] == 'Pcga' ? 'Codigo' : 'Codigo_Niif';
        $campo['nombre'] = $_REQUEST['Tipo'] == 'Pcga' ? 'Nombre' : 'Nombre_Niif';
        $campo['debe'] = $_REQUEST['Tipo'] == 'Pcga' ? 'Debe' : 'Debe_NIIF';
        $campo['haber'] = $_REQUEST['Tipo'] == 'Pcga' ? 'Haber' : 'Haber_NIIF';

        return $campo;
    }


    function calcularNuevoSaldo($naturaleza, $saldo_anterior, $debito, $credito)
    {
        $nuevo_saldo = 0;

        if ($naturaleza == 'D') { // Si es naturaleza debito, suma, de lo contrario, resta
            $nuevo_saldo = ($saldo_anterior + $debito) - $credito;
        } else {
            $nuevo_saldo = ($saldo_anterior + $credito) - $debito;
        }

        return $nuevo_saldo;
    }

    public function pdf()
    {
        $tipo = (isset($_REQUEST['Tipo']) ? $_REQUEST['Tipo'] : '');
        $fecha_inicio = (isset($_REQUEST['Fecha_Inicial']) ? $_REQUEST['Fecha_Inicial'] : '');
        $fecha_fin = (isset($_REQUEST['Fecha_Final']) ? $_REQUEST['Fecha_Final'] : '');
        $id_centro_costo = (isset($_REQUEST['Centro_Costo']) ? $_REQUEST['Centro_Costo'] : '');
        $oItem = new complex('Configuracion', "Id_Configuracion", 1);
        $config = $oItem->getData();
        unset($oItem);
        ob_start();
        $campo = $this->getCampo();
        $condicion = strCondicions();
        $condicion_fecha = strCondicionFecha();
        $query = ingresosOperacionales($condicion, $condicion_fecha, $id_centro_costo);
        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $ingresos = $oCon->getData();
        unset($oCon);
        $query = costosVentas($condicion, $condicion_fecha, $id_centro_costo);
        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $costosVentas = $oCon->getData();
        unset($oCon);
        $query = gastosAdmin($condicion, $condicion_fecha, $id_centro_costo);
        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $gastosAdmin = $oCon->getData();
        unset($oCon);
        $query = gastosVentas($condicion, $condicion_fecha, $id_centro_costo);
        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $gastosVentas = $oCon->getData();
        unset($oCon);
        $query = ingresosNoOperacionales($condicion, $condicion_fecha, $id_centro_costo);
        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $ingresosNoOper = $oCon->getData();
        unset($oCon);
        $query = gastosNoOperacionales($condicion, $condicion_fecha, $id_centro_costo);
        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $gastosNoOper = $oCon->getData();
        unset($oCon);
        $query = impuestos($condicion, $condicion_fecha, $id_centro_costo);
        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $impuestos = $oCon->getData();
        unset($oCon);
        $total_ingresos_operacionales = 0;
        $total_no_ingresos_operacionales = 0;
        $total_devoluciones = 0;
        $total_costos_ventas = 0;
        $total_gastos_admin = 0;
        $total_gastos_ventas = 0;
        $total_gastos_no_operacionales = 0;
        $total_impuestos = 0;
        $contenido = '
        <table class="table" style="width: 100%">
            <tr>
                <td style="font-weight:bold"></td>
                <td style="font-weight:bold">INGRESOS OPERACIONALES</td>
                <td style=""></td>
            </tr>';

        foreach ($ingresos as $i => $ingreso) {
            $saldo = $this->calcularNuevoSaldo($ingreso->Naturaleza, 0, $ingreso->{$campo['debe']}, $ingreso->{$campo['haber']});
            if ($saldo != 0) {
                $contenido .=
                    '<tr>
                    <td >' . $ingreso[$campo['codigo']] . '</td>
                    <td >' . $ingreso[$campo['nombre']] . '</td>
                    <td style="text-align:right">' . number_format($saldo, 2, ",", ".") . '</td>
                </tr>';
            }
            if ($ingreso->Tipo_Cta == 'Ingreso') {
                $total_ingresos_operacionales += $saldo;
            } elseif ($ingreso->Tipo_Cta == 'Devolucion') {
                $total_devoluciones += $saldo;
            } else {
                $total_ingresos_operacionales += $saldo;
            }
        }

        $ingresos_operacionales_netos = ($total_ingresos_operacionales + $total_devoluciones);

        // Arreglar este total...
        $contenido .= '
        <tr>
            <td style="font-weight:bold"></td>
            <td style="font-weight:bold">INGRESOS OPERACIONALES NETOS</td>
            <td style="text-align:right;border-top:1px solid #000"><b>' . number_format($ingresos_operacionales_netos, 2, ",", ".") . '</b></td>
        </tr>
        <tr>
            <td style="font-weight:bold"></td>
            <td style="font-weight:bold">COSTO DE VENTAS</td>
            <td></td>
        </tr>
        ';

        foreach ($costosVentas as $i => $costo) {
            $saldo = $this->calcularNuevoSaldo($costo->Naturaleza, 0, $costo->{$campo['debe']}, $costo->{$campo['haber']});
            if ($saldo != 0) {
                $contenido .= '
                <tr>
                    <td>' . $costo[$campo['codigo']] . '</td>
                    <td>' . $costo[$campo['nombre']] . '</td>
                    <td style="text-align:right">' . number_format($saldo, 2, ",", ".") . '</td>
                </tr>';
            }
            $total_costos_ventas += $saldo;
        }
        $utilidad_bruta_ventas = $ingresos_operacionales_netos - $total_costos_ventas;

        $contenido .= '
        <tr>
            <td style="font-weight:bold"></td>
            <td style="font-weight:bold"></td>
            <td></td>
        </tr>
        <tr>
            <td style="font-weight:bold"></td>
            <td style="font-weight:bold">Utilidad Bruta en Ventas</td>
            <td style="text-align:right;border-top:1px solid #000"><b>' . number_format($utilidad_bruta_ventas, 2, ",", ".") . '</b></td>
        </tr>
        <tr>
            <td style="font-weight:bold"></td>
            <td style="font-weight:bold"></td>
            <td></td>
        </tr>
        <tr>
            <td style="font-weight:bold"></td>
            <td style="font-weight:bold">GASTOS OPERACIONALES</td>
            <td></td>
        </tr>
        <tr>
            <td style="font-weight:bold"></td>
            <td style="font-weight:bold">De Admistración</td>
            <td></td>
        </tr>
      ';

        foreach ($gastosAdmin as $i => $gastoAdm) {
            $saldo = $this->calcularNuevoSaldo($gastoAdm->Naturaleza, 0, $gastoAdm->{$campo['debe']}, $gastoAdm->{$campo['haber']});
            if ($saldo != 0) {
                $contenido .= '
                <tr>
                    <td>' . $gastoAdm[$campo['codigo']] . '</td>
                    <td>' . $gastoAdm[$campo['nombre']] . '</td>
                    <td style="text-align:right">' . number_format($saldo, 2, ",", ".") . '</td>
                </tr>';
            }

            $total_gastos_admin += $saldo;
        }

        $contenido .= '<tr>
        <td style="font-weight:bold"></td>
        <td style="font-weight:bold">Total Gastos de administración</td>
        <td style="text-align:right;border-top:1px solid #000"><b>' . number_format($total_gastos_admin, 2, ",", ".") . '</b></td>
      </tr>
      <tr>
      <td style="font-weight:bold"></td>
        <td style="font-weight:bold"></td>
        <td></td>
      </tr>
      <tr>
        <td style="font-weight:bold"></td>
        <td style="font-weight:bold">De Ventas</td>
        <td></td>
      </tr>';

        foreach ($gastosVentas as $i => $gastoVta) {
            $saldo = $this->calcularNuevoSaldo($gastoVta->Naturaleza, 0, $gastoVta->{$campo['debe']}, $gastoVta->{$campo['haber']});
            if ($saldo != 0) {
                $contenido .= '<tr>
          <td>' . $gastoVta[$campo['codigo']] . '</td>
          <td>' . $gastoVta[$campo['nombre']] . '</td>
          <td style="text-align:right">' . number_format($saldo, 2, ",", ".") . '</td>
        </tr>';
            }

            $total_gastos_ventas += $saldo;
        }

        $total_gastos_operacionales = $total_gastos_admin + $total_gastos_ventas;

        $utilidad_operacional =  $utilidad_bruta_ventas - $total_gastos_operacionales;

        $contenido .= '<tr>
        <td style="font-weight:bold"></td>
        <td style="font-weight:bold">Total Gastos de Ventas</td>
        <td style="text-align:right;border-top:1px solid #000"><b>' . number_format($total_gastos_ventas, 2, ",", ".") . '</b></td>
      </tr>
      <tr>
        <td style="font-weight:bold"></td>
        <td style="font-weight:bold"></td>
        <td></td>
      </tr>
      <tr>
        <td style="font-weight:bold"></td>
        <td style="font-weight:bold">TOTAL GASTOS OPERACIONALES</td>
        <td style="text-align:right;border-top:1px solid #000"><b>' . number_format($total_gastos_operacionales, 2, ",", ".") . '</b></td>
      </tr>
      <tr>
        <td style="font-weight:bold"></td>
        <td style="font-weight:bold"></td>
        <td></td>
      </tr>
      <tr>
        <td style="font-weight:bold"></td>
        <td style="font-weight:bold">UTILIDAD OPERACIONAL</td>
        <td style="text-align:right;border-top:1px solid #000"><b>' . number_format($utilidad_operacional, 2, ",", ".") . '</b></td>
      </tr>
      <tr>
        <td style="font-weight:bold"></td>
        <td style="font-weight:bold"></td>
        <td></td>
      </tr>
      <tr>
        <td style="font-weight:bold"></td>
        <td style="font-weight:bold">INGRESOS NO OPERACIONALES</td>
        <td></td>
      </tr>';

        foreach ($ingresosNoOper as $i => $ingresoNoOp) {
            $saldo = $this->calcularNuevoSaldo($ingresoNoOp->Naturaleza, 0, $ingresoNoOp->{$campo['debe']}, $ingresoNoOp->{$campo['haber']});
            if ($saldo != 0) {
                $contenido .= '<tr>
          <td>' . $ingresoNoOp[$campo['codigo']] . '</td>
          <td>' . $ingresoNoOp[$campo['nombre']] . '</td>
          <td style="text-align:right">' . number_format($saldo, 2, ",", ".") . '</td>
        </tr>';
            }

            $total_no_ingresos_operacionales += $saldo;
        }

        $contenido .= '<tr>
      <td style="font-weight:bold"></td>
      <td style="font-weight:bold">Total Ingresos no Operacionales</td>
      <td style="text-align:right;border-top:1px solid #000"><b>' . number_format($total_no_ingresos_operacionales, 2, ",", ".") . '</b></td>
    </tr>
    <tr>
      <td style="font-weight:bold"></td>
      <td style="font-weight:bold"></td>
      <td></td>
    </tr>
    <tr>
        <td style="font-weight:bold"></td>
        <td style="font-weight:bold">GASTOS NO OPERACIONALES</td>
        <td></td>
      </tr>';

        foreach ($gastosNoOper as $i => $gastoNoOp) {
            $saldo = $this->calcularNuevoSaldo($gastoNoOp->Naturaleza, 0, $gastoNoOp->{$campo['debe']}, $gastoNoOp->{$campo['haber']});
            if ($saldo != 0) {
                $contenido .= '<tr>
          <td>' . $gastoNoOp[$campo['codigo']] . '</td>
          <td>' . $gastoNoOp[$campo['nombre']] . '</td>
          <td style="text-align:right">' . number_format($saldo, 2, ",", ".") . '</td>
        </tr>';
            }

            $total_gastos_no_operacionales += $saldo;
        }

        $utilidad_antes_impuestos = $utilidad_operacional + $total_no_ingresos_operacionales - $total_gastos_no_operacionales;

        $contenido .= '<tr>
            <td style="font-weight:bold"></td>
            <td style="font-weight:bold">Total Gastos no Operacionales</td>
            <td style="text-align:right;border-top:1px solid #000"><b>' . number_format($total_gastos_no_operacionales, 2, ",", ".") . '</b></td>
            </tr>
            <tr>
            <td style="font-weight:bold"></td>
            <td style="font-weight:bold"></td>
            <td></td>
            </tr>
            <tr>
                <td style="font-weight:bold"></td>
                <td style="font-weight:bold">Utilidad Antes de Impuestos</td>
                <td style="text-align:right">' . number_format($utilidad_antes_impuestos, 2, ",", ".") . '</td>
            </tr>
            <tr>
            <td style="font-weight:bold"></td>
            <td style="font-weight:bold"></td>
            <td></td>
            </tr>';

        foreach ($impuestos as $i => $impuesto) {
            $saldo = $this->calcularNuevoSaldo($impuesto->Naturaleza, 0, $impuesto->{$campo['debe']}, $impuesto->{$campo['haber']});
            if ($saldo != 0) {
                $contenido .= '<tr>
        <td>' . $impuesto[$campo['codigo']] . '</td>
        <td>' . $impuesto[$campo['nombre']] . '</td>
        <td style="text-align:right">' . number_format($saldo, 2, ",", ".") . '</td>
      </tr>';
            }

            $total_impuestos += $saldo;
        }

        $utilidad_del_ejercicio = $utilidad_antes_impuestos - $total_impuestos;

        $contenido .= '
    <tr>
      <td style="font-weight:bold"></td>
      <td style="font-weight:bold">Total Impuestos</td>
      <td style="text-align:right;border-top:1px solid #000"><b>' . number_format($total_impuestos, 2, ",", ".") . '</b></td>
    </tr>
    <tr>
      <td style="font-weight:bold"></td>
      <td style="font-weight:bold"></td>
      <td></td>
    </tr>
    <tr>
        <td style="font-weight:bold"></td>
        <td style="font-weight:bold">UTILIDAD DEL EJERCICIO</td>
        <td style="text-align:right;border-top:1px solid #000"><b>' . number_format($utilidad_del_ejercicio, 2, ",", ".") . '</b></td>
      </tr></table>';

        $contenido .= '
      <table style="margin-top:50px;">
      <tr>
      <td style="font-weight:bold;text-align:center">
      ___________________________ <br>
      FIRMA GERENTE
      </td>
      <td style="font-weight:bold;text-align:center">
      ___________________________ <br>
      FIRMA CONTADOR
      </td>
      <td style="font-weight:bold;text-align:center">
      ___________________________ <br>
      FIRMA REVISOR FISCAL
      </td>
      </tr>
      </table>
      ';


        /* CONTENIDO GENERAL DEL ARCHIVO MEZCLANDO TODA LA INFORMACION*/
        $content = '<page backtop="0mm" backbottom="0mm">
                <div class="page-content" >' .

            $contenido . '
                </div>
            </page>';
        $company = Company::first();
        $image = $company->page_heading;
        $datosCabecera = (object) array(
            'Titulo' => 'Estado resultado',
            'Codigo' => '',
            'Fecha' => $this->fecha($fecha_fin),
            'CodigoFormato' => ''
        );
        $pdf = PDF::loadView('pdf.estado_resultado', [
            'content' => $content,
            'company' => $company,
            'datosCabecera' => $datosCabecera,
            'image' => $image
        ]);
        return $pdf->download('estado_resultado.pdf');
        //return $content;
    }

    public function excel()
    {
        $tipo = (isset($_REQUEST['Tipo']) ? $_REQUEST['Tipo'] : '');
        $fecha_inicio = (isset($_REQUEST['Fecha_Inicial']) ? $_REQUEST['Fecha_Inicial'] : '');
        $fecha_fin = (isset($_REQUEST['Fecha_Final']) ? $_REQUEST['Fecha_Final'] : '');
        $id_centro_costo = (isset($_REQUEST['Centro_Costo']) ? $_REQUEST['Centro_Costo'] : '');


        /* DATOS GENERALES DE CABECERAS Y CONFIGURACION */
        $oItem = new complex('Configuracion', "Id_Configuracion", 1);
        $config = $oItem->getData();
        unset($oItem);
        /* FIN DATOS GENERALES DE CABECERAS Y CONFIGURACION */



        $campo = $this->getCampo();
        $condicion = strCondicions();
        $condicion_fecha = strCondicionFecha();

        $query = ingresosOperacionales($condicion, $condicion_fecha, $id_centro_costo);

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $ingresos = $oCon->getData();
        unset($oCon);

        $query = costosVentas($condicion, $condicion_fecha, $id_centro_costo);

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $costosVentas = $oCon->getData();
        unset($oCon);

        $query = gastosAdmin($condicion, $condicion_fecha, $id_centro_costo);

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $gastosAdmin = $oCon->getData();
        unset($oCon);

        $query = gastosVentas($condicion, $condicion_fecha, $id_centro_costo);

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $gastosVentas = $oCon->getData();
        unset($oCon);

        $query = ingresosNoOperacionales($condicion, $condicion_fecha, $id_centro_costo);

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $ingresosNoOper = $oCon->getData();
        unset($oCon);

        $query = gastosNoOperacionales($condicion, $condicion_fecha, $id_centro_costo);

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $gastosNoOper = $oCon->getData();
        unset($oCon);

        $query = impuestos($condicion, $condicion_fecha, $id_centro_costo);

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $impuestos = $oCon->getData();
        unset($oCon);

        $total_ingresos_operacionales = 0;
        $total_no_ingresos_operacionales = 0;
        $total_devoluciones = 0;
        $total_costos_ventas = 0;
        $total_gastos_admin = 0;
        $total_gastos_ventas = 0;
        $total_gastos_no_operacionales = 0;
        $total_impuestos = 0;



        $contenido = '<table class="table">
      <tr>
        <td colspan="3" align="center">
          <strong>ESTADO DE RESULTADOS | ' . $this->fecha($fecha_inicio) . ' - ' . $this->fecha($fecha_fin) . '</strong>
        </td>
      </tr>
      <tr>
        <td ></td>
        <td ><strong>INGRESOS OPERACIONALES</strong></td>
        <td ></td>
      </tr>';

        foreach ($ingresos as $i => $ingreso) {
            $saldo = $this->calcularNuevoSaldo($ingreso->Naturaleza, 0, $ingreso->{$campo['debe']}, $ingreso->{$campo['haber']});
            if ($saldo != 0) {
                $contenido .= '<tr>
        <td >' . $ingreso[$campo['codigo']] . '</td>
        <td >' . $ingreso[$campo['nombre']] . '</td>
        <td >' . number_format($saldo, 2, ",", ".") . '</td>
      </tr>';
            }
            if ($ingreso->Tipo_Cta == 'Ingreso') {
                $total_ingresos_operacionales += $saldo;
            } elseif ($ingreso->Tipo_Cta == 'Devolucion') {
                $total_devoluciones += $saldo;
            } else {
                $total_ingresos_operacionales += $saldo;
            }
        }

        $ingresos_operacionales_netos = ($total_ingresos_operacionales + $total_devoluciones);

        // Arreglar este total...
        $contenido .= '<tr>
        <td ></td>
        <td ><strong>INGRESOS OPERACIONALES NETOS</strong></td>
        <td ><b>' . number_format($ingresos_operacionales_netos, 2, ",", ".") . '</b></td>
      </tr>
      <tr>
        <td ></td>
        <td ><strong>COSTO DE VENTAS</strong></td>
        <td ></td>
      </tr>
      ';

        foreach ($costosVentas as $i => $costo) {
            $saldo = $this->calcularNuevoSaldo($costo->Naturaleza, 0, $costo->{$campo['debe']}, $costo->{$campo['haber']});
            if ($saldo != 0) {
                $contenido .= '<tr>
          <td >' . $costo[$campo['codigo']] . '</td>
          <td >' . $costo[$campo['nombre']] . '</td>
          <td >' . number_format($saldo, 2, ",", ".") . '</td>
        </tr>';
            }

            $total_costos_ventas += $saldo;
        }

        $utilidad_bruta_ventas = $ingresos_operacionales_netos - $total_costos_ventas;

        $contenido .= '
      <tr>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
      </tr>
      <tr>
        <td ></td>
        <td ><strong>Utilidad Bruta en Ventas</strong></td>
        <td ><strong>' . number_format($utilidad_bruta_ventas, 2, ",", ".") . '</strong></td>
      </tr>
      <tr>
      <td ></td>
        <td ></td>
        <td ></td>
      </tr>
      <tr>
        <td ></td>
        <td ><strong>GASTOS OPERACIONALES</strong></td>
        <td ></td>
      </tr>
      <tr>
        <td ></td>
        <td ><strong>De Admistraci&oacute;n</strong></td>
        <td ></td>
      </tr>
      ';

        foreach ($gastosAdmin as $i => $gastoAdm) {
            $saldo = $this->calcularNuevoSaldo($gastoAdm->Naturaleza, 0, $gastoAdm->{$campo['debe']}, $gastoAdm->{$campo['haber']});
            if ($saldo != 0) {
                $contenido .= '<tr>
          <td >' . $gastoAdm[$campo['codigo']] . '</td>
          <td >' . $gastoAdm[$campo['nombre']] . '</td>
          <td >' . number_format($saldo, 2, ",", ".") . '</td>
        </tr>';
            }

            $total_gastos_admin += $saldo;
        }

        $contenido .= '<tr>
        <td ></td>
        <td ><strong>Total Gastos de administraci&oacute;n</strong></td>
        <td ><b>' . number_format($total_gastos_admin, 2, ",", ".") . '</b></td>
      </tr>
      <tr>
      <td ></td>
        <td ></td>
        <td ></td>
      </tr>
      <tr>
        <td ></td>
        <td ><strong>De Ventas</strong></td>
        <td ></td>
      </tr>';

        foreach ($gastosVentas as $i => $gastoVta) {
            $saldo = $this->calcularNuevoSaldo($gastoVta->Naturaleza, 0, $gastoVta->{$campo['debe']}, $gastoVta->{$campo['haber']});
            if ($saldo != 0) {
                $contenido .= '<tr>
          <td >' . $gastoVta[$campo['codigo']] . '</td>
          <td >' . $gastoVta[$campo['nombre']] . '</td>
          <td >' . number_format($saldo, 2, ",", ".") . '</td>
        </tr>';
            }

            $total_gastos_ventas += $saldo;
        }

        $total_gastos_operacionales = $total_gastos_admin + $total_gastos_ventas;

        $utilidad_operacional =  $utilidad_bruta_ventas - $total_gastos_operacionales;

        $contenido .= '<tr>
        <td ></td>
        <td ><strong>Total Gastos de Ventas</strong></td>
        <td ><b>' . number_format($total_gastos_ventas, 2, ",", ".") . '</b></td>
      </tr>
      <tr>
        <td ></td>
        <td ></td>
        <td ></td>
      </tr>
      <tr>
        <td ></td>
        <td ><strong>TOTAL GASTOS OPERACIONALES</strong></td>
        <td ><b>' . number_format($total_gastos_operacionales, 2, ",", ".") . '</b></td>
      </tr>
      <tr>
        <td ></td>
        <td ></td>
        <td ></td>
      </tr>
      <tr>
        <td ></td>
        <td ><strong>UTILIDAD OPERACIONAL</strong></td>
        <td ><b>' . number_format($utilidad_operacional, 2, ",", ".") . '</b></td>
      </tr>
      <tr>
        <td ></td>
        <td ></td>
        <td ></td>
      </tr>
      <tr>
        <td ></td>
        <td ><strong>INGRESOS NO OPERACIONALES</strong></td>
        <td ></td>
      </tr>';

        foreach ($ingresosNoOper as $i => $ingresoNoOp) {
            $saldo = $this->calcularNuevoSaldo($ingresoNoOp->Naturaleza, 0, $ingresoNoOp->{$campo['debe']}, $ingresoNoOp->{$campo['haber']});
            if ($saldo != 0) {
                $contenido .= '<tr>
          <td >' . $ingresoNoOp[$campo['codigo']] . '</td>
          <td >' . $ingresoNoOp[$campo['nombre']] . '</td>
          <td >' . number_format($saldo, 2, ",", ".") . '</td>
        </tr>';
            }

            $total_no_ingresos_operacionales += $saldo;
        }

        $contenido .= '<tr>
      <td ></td>
      <td ><strong>Total Ingresos no Operacionales</strong></td>
      <td ><b>' . number_format($total_no_ingresos_operacionales, 2, ",", ".") . '</b></td>
    </tr>
    <tr>
      <td ></td>
      <td ></td>
      <td ></td>
    </tr>
    <tr>
        <td ></td>
        <td ><strong>GASTOS NO OPERACIONALES</strong></td>
        <td ></td>
      </tr>';

        foreach ($gastosNoOper as $i => $gastoNoOp) {
            $saldo = $this->calcularNuevoSaldo($gastoNoOp->Naturaleza, 0, $gastoNoOp->{$campo['debe']}, $gastoNoOp->{$campo['haber']});
            if ($saldo != 0) {
                $contenido .= '<tr>
          <td >' . $gastoNoOp[$campo['codigo']] . '</td>
          <td >' . $gastoNoOp[$campo['nombre']] . '</td>
          <td >' . number_format($saldo, 2, ",", ".") . '</td>
        </tr>';
            }

            $total_gastos_no_operacionales += $saldo;
        }

        $utilidad_antes_impuestos = $utilidad_operacional + $total_no_ingresos_operacionales - $total_gastos_no_operacionales;

        $contenido .= '<tr>
      <td ></td>
      <td ><strong>Total Gastos no Operacionales</strong></td>
      <td ><b>' . number_format($total_gastos_no_operacionales, 2, ",", ".") . '</b></td>
    </tr>
    <tr>
      <td ></td>
      <td ></td>
      <td ></td>
    </tr>
    <tr>
        <td ></td>
        <td ><strong>Utilidad Antes de Impuestos</strong></td>
        <td >' . number_format($utilidad_antes_impuestos, 2, ",", ".") . '</td>
      </tr>
      <tr>
      <td ></td>
      <td ></td>
      <td ></td>
    </tr>';

        foreach ($impuestos as $i => $impuesto) {
            $saldo = $this->calcularNuevoSaldo($impuesto->Naturaleza, 0, $impuesto->{$campo['debe']}, $impuesto->{$campo['haber']});
            if ($saldo != 0) {
                $contenido .= '<tr>
        <td >' . $impuesto[$campo['codigo']] . '</td>
        <td >' . $impuesto[$campo['nombre']] . '</td>
        <td >' . number_format($saldo, 2, ",", ".") . '</td>
      </tr>';
            }

            $total_impuestos += $saldo;
        }

        $utilidad_del_ejercicio = $utilidad_antes_impuestos - $total_impuestos;

        $contenido .= '
    <tr>
      <td ></td>
      <td ><strong>Total Impuestos</strong></td>
      <td ><b>' . number_format($total_impuestos, 2, ",", ".") . '</b></td>
    </tr>
    <tr>
      <td ></td>
      <td ></td>
      <td ></td>
    </tr>
    <tr>
        <td ></td>
        <td ><strong>UTILIDAD DEL EJERCICIO</strong></td>
        <td ><b>' . number_format($utilidad_del_ejercicio, 2, ",", ".") . '</b></td>
      </tr>
      <tr>
      <td >&nbsp;</td>
      <td >&nbsp;</td>
      <td >&nbsp;</td>
      </tr>
      <tr>
      <td >&nbsp;</td>
      <td >&nbsp;</td>
      <td >&nbsp;</td>
      </tr></table>';


        $contenido .= '
      <table style="margin-top:50px;">
      <tr>
      <td >
      <strong>_____________________________</strong> <br>
      <strong>FIRMA GERENTE</strong>
      </td>
      <td >
      <strong>_____________________________</strong> <br>
      <strong>FIRMA CONTADOR</strong>
      </td>
      <td >
      <strong>_____________________________</strong> <br>
      <strong>FIRMA REVISOR FISCAL</strong>
      </td>
      </tr>
      </table>
      ';
      //return $contenido;
      return Excel::download(new EstadoResultadoExport($contenido), 'estado-resultado.xlsx');
    }
}
