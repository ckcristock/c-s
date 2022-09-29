<?php

namespace App\Http\Controllers;

use App\Http\Libs\Nomina\Facades\NominaLiquidacion;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\Company;
use App\Traits\ApiResponser;

class LiquidacionesController extends Controller
{
    use ApiResponser;

    /**
     * Retornar objeto JSON con calculo inicial de la liquidación del funcionario siendo posible modificar 
     * la fecha de liquidación por parte del usuario, por defecto este parámetro es null
     *
     * @param integer $id
     * @param string $fechaFin
     * @return  Illuminate\Support\Facades\Response
     */
    public function get($id, $fechaFin = null)
    {
        return NominaLiquidacion::liquidacionFuncionarioWithId($id)
            ->until($fechaFin)->calculate()
            ->makeResponse();
    }

    /**
     * Retornar objeto JSON con cálculo de liquidación modificando los días acumulados de vacaciones del 
     * funcionario,esta acción es realizada por el usuario
     *
     * @param int $id
     * @param string $fechaFin
     * @param  float $diasAcumulados
     * @return Illuminate\Support\Facades\Response
     */
    public function getWithVacacionesActuales($id)
    {
        $fechaFin = null;
        $diasAcumulados = 0;
        if (request()->has(['diasAcumulados', 'fechaFin'])) {
            $fechaFin = request('fechaFin');
            $diasAcumulados = request('diasAcumulados');
        }
        return
            NominaLiquidacion::liquidacionFuncionarioWithId($id)
            ->until($fechaFin)
            ->withVacacionesActuales($diasAcumulados)
            ->calculate()
            ->makeResponse();
    }


    /**
     * Retornar objeto JSON  con cálculo de liquidación modificando el salario base del funcionario, esta acción es realizada por el usuario
     *
     * @param integer $id
     * @param string $fechaFin
     * @param integer $salarioBase
     * @return Illuminate\Support\Facades\Response
     */
    public function getWithSalarioBase($id)
    {
        $fechaFin = null;
        $salarioBase = 0;

        if (request()->has(['fechaFin', 'salarioBase'])) {
            $fechaFin = request('fechaFin');
            $salarioBase = request('salarioBase');
        }

        return NominaLiquidacion::liquidacionFuncionarioWithId($id)->until($fechaFin)->withSalarioBase($salarioBase)->calculate()->makeResponse();
    }


    /**
     * Retornar objeto JSON con cálculo de liquidación modificando la bases, esta acción es realizada por el usuario
     *
     * @param int $id
     * @return Illuminate\Support\Facades\Response
     */
    public function getWithBases($id)
    {
        $fechaRetiro = null;
        $salarioBase = 0;
        $baseVacaciones =  0;
        $basePrima = 0;
        $baseCesantias = 0;

        if (request()->has(['fechaRetiro', 'salarioBase', 'baseVacaciones', 'basePrima', 'baseCesantias'])) {
            $fechaRetiro = request('fechaRetiro');
            $salarioBase = request('salarioBase');
            $baseVacaciones = request('baseVacaciones');
            $baseCesantias = request('baseCesantias');
            $basePrima = request('basePrima');
        }

        return NominaLiquidacion::liquidacionFuncionarioWithId($id)->until($fechaRetiro)
            ->withSalarioBase($salarioBase)
            ->withBaseVacaciones($baseVacaciones)
            ->withBaseCesantias($baseCesantias)
            ->withBasePrima($basePrima)
            ->calculate()
            ->makeResponse();
    }

    /**
     * Retornar objeto JSON con cálculo de liquidación añadiendo o suprimiendo ingresos y egresos, esta acción es realizada por el usuario
     *
     * @param int $id
     * @return Illuminate\Support\Facades\Response
     */
    public function getWithIngresos($id)
    {
        $fechaRetiro = '';
        $ingresos = 0;
        $egresos = 0;

        if (request()->has(['ingresos', 'egresos'])) {
            $fechaRetiro = request('fechaRetiro');
            $ingresos = request('ingresos');
            $egresos = request('egresos');
        }

        return NominaLiquidacion::liquidacionFuncionarioWithId($id)
            ->until($fechaRetiro)
            ->withIngresos($ingresos)
            ->withEgresos($egresos)
            ->calculate()
            ->makeResponse();
    }

    public function getPdfLiquidacion()
    {
        $funcionario = null;
        $nombrePdf = null;
        $empresa = Company::first()['razon_social'];

        if (request()->has('funcionario')) {

            $funcionario = request('funcionario');
            // $nombrePdf = $funcionario['nombres'] . '_' . $funcionario['apellidos'] . '.pdf';
            $nombrePdf =  'liqejemplo.pdf';
            // return view('nomina.liquidacion', compact('funcionario'));
        }
        $funcionario['empresa'] = $empresa;
        return PDF::loadView('nomina.liquidacion', compact('funcionario'))->download($nombrePdf);
    }
}
