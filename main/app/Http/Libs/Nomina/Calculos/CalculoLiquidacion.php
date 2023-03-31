<?php

namespace App\Http\Libs\Nomina\Calculos;

use App\Models\Loan;
use Carbon\Carbon;
use App\Models\PayrollFactor;
use App\Models\WorkContract;
/**
 * Clase para realizar el cálculo de la liquidación de x funcionario
 */
class CalculoLiquidacion
{
    /**
     * Constantes para cálculos de vacaciones, cesantías e intereses de cesantías
     */
    const FACTOR_VACACIONES = 0.0416667; /*  1.25 dias vacaciones por cada 30 dias de trabajo */
    const FACTOR_CESANTIAS_PRIMA = 0.0833333;
    const FACTOR_INTERESES_CESANTIAS = 0.1200;

    /**
     * Salario base del funcionario
     *
     * @var integer
     */
    protected $salarioBase;
    protected $salarioBaseModified;
    protected $id;
    /**
     * Valor de auxilio de transporte
     *
     * @var integer
     */
    protected $auxilioTransporte = 0;

    /**
     * Base para calcular cesantias
     *
     * @var integer
     */
    protected $baseCesantias;
    protected $baseCesantiasModified = 0;

    /**
     * Base para calcular prima
     *
     * @var integer
     */
    protected $basePrima;
    protected $basePrimaModified = 0;

    /**
     * Base para calcular vacaciones
     *
     * @var integer
     */
    protected $baseVacaciones;
    protected $baseVacacionesModified = 0;

    /**
     * Días acumulados de vacaciones del funcionario hasta la fecha
     *
     * @var float
     */
    protected $vacacionesActuales;
    protected $vacacionesActualesModified = 0;
    protected $vacacionesDisfrutadas = 0;

    /**
     * Dias acumulados de vacaciones del funcionario que tuvo en el último periodo de pago
     *
     * @var float
     */
    protected $vacacionesUltimoPeriodo;

    /**
     * Fecha de ingreso (contratación) del funcionario
     *
     * @var  string
     */
    protected $fechaIngreso;

    /**
     * Fecha de la liquidación o de retiro del funcionario
     *
     * @var  [string, Carbon]
     */
    protected $fechaRetiro;

    /**
     * Días a liquidar del funcionario
     *
     * @var integer
     */
    protected $diasLiquidacion;

    /**
     * Días trabajados para el cálculo de las cesantías
     *
     * @var integer
     */
    protected $diasCesantias;

    /**
     * Días trabajados para el cálculo de la prima
     *
     * @var integer
     */
    protected $diasPrima;


    /**
     * Total a pagar por concepto de cesantías
     *
     * @var integer
     */
    protected $totalCesantias;

    /**
     * Total a pagar por concepto de intereses de cesantías
     *
     * @var  integer
     */
    protected $totalInteresesCesantias;
    /**
     * Total deuda de préstamos
     *
     * @var  integer
     */
    protected $totalPrestamos;

    /**
     * Total a pagar por concepto de vacaciones
     *
     * @var integer
     */
    protected $totalVacaciones;

    /**
     * Total a pagar por concepto de prima
     *
     * @var integer
     */
    protected $totalPrima;

    /**
     * Total a añadir al pago por concepto de  ingresos
     *
     * @var integer
     */
    protected $totalIngresos = 0;

    /**
     * Total a retener al pago por concepto de egresos
     *
     * @var integer
     */
    protected $totalEgresos = 0;

    /**
     * Total a pagar por indemnizacion si aplica por justa causa
     *
     * @var integer
     */
    protected $indemnizacion;

    /**
     * Total pago de liquidación
     *
     * @var int
     */
    protected $totalLiquidacion;


    public function __construct($salarioBase = 0, $auxilioTransporte = 0, $fechaIngreso, $fechaRetiro = null, $id, $diasLiquidacion = 0, $vacacionesActuales=0, $vacacionesActualesModified= 0)
    {
        $this->salarioBase = $salarioBase;
        $this->auxilioTransporte = $auxilioTransporte;
        $this->fechaIngreso = $fechaIngreso;
        $this->fechaRetiro = $fechaRetiro == null ? Carbon::now() : Carbon::parse($fechaRetiro);
        $this->id = $id;
        $this->diasLiquidacion =$diasLiquidacion;
        $this->vacacionesActuales =$vacacionesActuales;
        $this->vacacionesActualesModified = $vacacionesActualesModified;

    }

    /**
     * Settear valor de los días acumulados de vacaciones del funcionario
     * Este valor modifica al valor obtenido del cálculo de vacaciones actuales
     *
     * @param integer $vacaciones
     * @return void
     */
    public function setVacacionesActuales($vacaciones = 0)
    {
        $this->vacacionesActualesModified = $vacaciones;
    }

    /**
     * Settear el salario base del funcionario
     * Este valor modifica al salario base obtenido del funcionario
     *
     * @param integer $salarioBase
     * @return void
     */
    public function setSalarioBase($salarioBase = 0)
    {
        $this->salarioBase = $salarioBase;
    }

    /**
     * Settear la base de vacaciones
     * Este valor modifica al valor obtenido del cálculo de base de vacaciones
     *
     * @param integer $baseVacaciones
     * @return void
     */
    public function setBaseVacaciones($baseVacaciones = 0)
    {
        $this->baseVacacionesModified = $baseVacaciones;
    }

    /**
     * Settear la base de cesantías
     * Este valor modifica al valor obtenida del cálculo de base de cesantías
     *
     * @param integer $baseCesantias
     * @return void
     */
    public function setBaseCesantias($baseCesantias = 0)
    {
        $this->baseCesantiasModified = $baseCesantias;
    }

    /**
     * Settear la base de la prima
     * Este valor modifica al valor obtenido del cálculo de base de prima
     *
     * @param integer $basePrima
     * @return void
     */
    public function setBasePrima($basePrima = 0)
    {
        $this->basePrimaModified = $basePrima;
    }

    /**
     * Setter valor indemnizacion
     *
     * @param integer $indemnizacion
     * @return void
     */
    public function setIndemnizacion($indemnizacion = 0)
    {
        $this->indemnizacion = $indemnizacion;
    }

    /**
     * Calcular los días a liquidar del funcionario
     *
     * @return void
     */
    public function calcularDiasLiquidacion()
    {
        $this->diasLiquidacion = $this->fechaRetiro->diffInDays(Carbon::parse($this->fechaIngreso));
    }

    /**
     * Calcular dias trabajados para el pago de cesantias
     *
     * @return void
     */
    public function calcularDiasCesantias()
    {
        $fechaInicioAnio = Carbon::now()->startOfYear();
        if (Carbon::parse($this->fechaIngreso) > $fechaInicioAnio) {
            $fechaInicioAnio = Carbon::parse($this->fechaIngreso);
        }

        $this->diasCesantias = $this->fechaRetiro->diffInDays($fechaInicioAnio);
    }


    public function calcularDiasPrima()
    {
        $fechaSemestre = null;

        if ($this->fechaRetiro < Carbon::now()->startOfYear()->addMonths(6)) {
            $fechaSemestre = Carbon::now()->startOfYear();
        } else {
            $fechaSemestre = Carbon::now()->startOfYear()->addMonths(6);
        }

        if (Carbon::parse($this->fechaIngreso) > $fechaSemestre) {
            $fechaSemestre = Carbon::parse($this->fechaIngreso);
        }

        $this->diasPrima = $this->fechaRetiro->diffInDays($fechaSemestre);
    }

    /**
     * Calcular la base para el pago de cesantías
     *
     * @return void
     */
    public function calcularBaseCesantias()
    {
        if ($this->baseCesantiasModified > 0) {
            $this->baseCesantias = $this->baseCesantiasModified;
        } else {

            $this->baseCesantias = $this->salarioBase + $this->auxilioTransporte;
        }
    }

    /**
     * Calcular la base para el pago de prima
     *
     * @return void
     */
    public function calcularBasePrima()
    {
        if ($this->basePrimaModified > 0) {
            $this->basePrima = $this->basePrimaModified;
        } else {

            $this->basePrima = $this->salarioBase + $this->auxilioTransporte;
        }
    }

    /**
     * Calcular la base para el pago de vacaciones
     *
     * @return void
     */
    public function calcularBaseVacaciones()
    {
        if ($this->baseVacacionesModified > 0) {
            $this->baseVacaciones = $this->baseVacacionesModified;
        } else {
            $this->baseVacaciones = $this->salarioBase;
        }
    }

    /**
     * Calcular los días de vacaciones del funcionario hasta la fecha
     *
     * @return void
     */
    public function calcularVacacionesActuales()
    {

        $payrollFactors = PayrollFactor::where('person_id', $this->id)->where('disability_type', 'Vacaciones')->get();

        if ($payrollFactors->isEmpty()) {
            $this->vacacionesDisfrutadas = 0;
        } else {
            foreach ($payrollFactors as $payrollFactor) {
                $startDate = $payrollFactor->date_start;
                $endDate = $payrollFactor->date_end;
                $preliquidated = PreliquidatedLog::where('person_id', $this->id)->latest()->get();

                $workContract = WorkContract::where('id',$preliquidated->person_work_contract_id)
                    ->where('date_of_admission', '<=', $endDate)
                    ->where(function ($query) use ($startDate) {
                        $query->where('date_end', '>=', $startDate)->orWhereNull('date_end');
                    })
                    ->first();

                if ($workContract) {
                    $this->vacacionesDisfrutadas += $payrollFactor->number_days;
                }
            }
        }

        if ($this->vacacionesActualesModified > 0 && $this->vacacionesActualesModified) {
            $this->vacacionesActuales = $this->vacacionesActualesModified;
        } else {
            $this->vacacionesActuales = number_format($this->diasLiquidacion * self::FACTOR_VACACIONES - $this->vacacionesDisfrutadas, 1, '.', '');

        }
    }

    /**
     * Settear los días de vacaciones del funcionario en el último periodo de pago, esto viene desde BD
     *
     * @param array $vacaciones
     * @param boolean $existenVacaciones
     * @return void
     */
    public function setVacacionesUltimoPeriodo($vacaciones, $existenVacaciones)
    {

        if (!$existenVacaciones) {
            $this->vacacionesUltimoPeriodo = $this->vacacionesActuales;
        } else {
            $this->vacacionesUltimoPeriodo = $vacaciones;
        }
    }

    /**
     * Calcular el total de pago por concepto de vacaciones
     *
     * @return void
     */
    public function calcularTotalVacaciones()
    {
        $this->totalVacaciones = round($this->baseVacaciones / 30 * $this->vacacionesActuales, 0, PHP_ROUND_HALF_UP);
    }

    /**
     * Calcular el total de pago por concepto de cesantías
     *
     * @return void
     */
    public function calcularTotalCesantias()
    {

        /*
    $severance_payments = SeverancePaymentPerson::where('people_id', id)->with('severancePayment')->get();
    $years = array();
    foreach ($severance_payments as $severance_payment) {
        $years[] = $severance_payment->severancePayment->year;
    }
    $current_year = date('Y');
    if (in_array($current_year, $years)) {
        $cesantias_anteriores = 0;
    } else {
        $cesantias_anteriores = 1; falta saber de donde llega ese total por usuario 
    }
    
} */
        /* Si ya se pagaron las cesantias del año anteior  */
        $this->totalCesantias = round($this->baseCesantias * $this->diasCesantias / 360, 0, PHP_ROUND_HALF_UP);
    }

    /**
     * Calcular el total de pago por concepto de intereses a las cesantias
     *
     * @return void
     */
    public function calcularTotalInteresesCesantias()
    {
        $this->totalInteresesCesantias = round($this->totalCesantias * self::FACTOR_INTERESES_CESANTIAS * ($this->diasCesantias / 360), 0, PHP_ROUND_HALF_UP);
    }

    /**
     * Calcular el total de pago por concepto de prima
     *
     * @return void
     */
    public function calcularTotalPrima()
    {
        $this->totalPrima = round($this->basePrima * $this->diasPrima / 360, 0, PHP_ROUND_HALF_UP);
    }


    /**
     * Calcular o settear total de ingresos
     *
     * @param integer $ingresos
     * @return void
     */
    public function setIngresos($ingresos = 0)
    {
        $this->totalIngresos = $ingresos;
    }

    /**
     * Calcular o settear total de egresos
     *
     * @param integer $egresos
     * @return void
     */
    public function setEgresos($egresos = 0)
    {
        $this->totalEgresos = $egresos;
    }

    /**
     * Calcular préstamos
     *
     * @param integer $totalPrestamos
     * @return void
     */
    public function calcularPrestamos()
    {
        $prestamos = Loan::where('person_id', $this->id)->get();
        $deuda = 0;
        foreach ($prestamos as $prestamo){
            $deuda += $prestamo->value;
        }
        $this->totalPrestamos = $deuda;
    }

    /**
     * Calcular el total liquidación del funcionario
     *
     * @return void
     */
    public function calcularTotalLiquidacion()
    {
        $this->totalLiquidacion =
            $this->totalVacaciones +
            $this->totalCesantias +
            $this->totalInteresesCesantias +
            $this->totalPrima +
            $this->totalIngresos -
            $this->totalEgresos -
            $this->totalPrestamos;
    }

    /**
     * Getter salario base
     *
     * @return integer
     */
    public function getSalarioBase()
    {
        return $this->salarioBase;
    }

    /**
     * Getter fecha de retiro
     *
     * @return string
     */
    public function getFechaRetiro()
    {
        return $this->fechaRetiro->toDateString();
    }

    /**
     * Getter días de liquidación
     *
     * @return integer
     */
    public function getDiasLiquidacion()
    {
        return $this->diasLiquidacion;
    }

    /**
     * Getter dias cesantías
     *
     * @return integer
     */
    public function getDiasCesantias()
    {
        return $this->diasCesantias;
    }

    /**
     * Getter base cesantias
     *
     * @return integer
     */
    public function getBaseCesantias()
    {
        return $this->baseCesantias;
    }

    /**
     * Getter base prima
     *
     * @return integer
     */
    public function getBasePrima()
    {
        return $this->basePrima;
    }

    /**
     * Getter base vacaciones
     *
     * @return integer
     */
    public function getBaseVacaciones()
    {
        return $this->baseVacaciones;
    }

    /**
     * Getter vacaciones actuales
     *
     * @return void
     */
    public function getVacacionesActuales()
    {
        return $this->vacacionesActuales;
    }

    public function getVacacionesUltimoPeriodo()
    {
        return $this->vacacionesUltimoPeriodo;
    }

    /**
     * Getter total vacaciones
     *
     * @return integer
     */
    public function getTotalVacaciones()
    {
        return $this->totalVacaciones;
    }

    /**
     * Getter total cesantias
     *
     * @return integer
     */
    public function getTotalCesantias()
    {
        return $this->totalCesantias;
    }

    /**
     * Getter total intereses cesantías
     *
     * @return integer
     */
    public function getTotalInteresesCesantias()
    {
        return $this->totalInteresesCesantias;
    }

    /**
     * Getter total prima
     *
     * @return integer
     */
    public function getTotalPrima()
    {
        return $this->totalPrima;
    }

    /**
     * Getter total prima
     *
     * @return integer
     */
    public function getPrestamos()
    {
        return $this->totalPrestamos;
    }

    /**
     * Getter total ingresos
     *
     * @return integer
     */
    public function getTotalIngresos()
    {
        return $this->totalIngresos;
    }

    /**
     * Getter total egresos
     *
     * @return integer
     */
    public function getTotalEgresos()
    {
        return $this->totalEgresos;
    }

    /**
     * Getter total liquidación
     *
     * @return integer
     */
    public function getTotalLiquidacion()
    {
        return $this->totalLiquidacion;
    }

    public function getTotalLiquidacionWithIndemnizacion()
    {
        return $this->totalLiquidacion + $this->indemnizacion;
    }
}


