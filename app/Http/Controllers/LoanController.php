<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Services\LoanService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class LoanController extends Controller
{
	//
	use ApiResponser;
	public function index()
	{
		return $this->success(
			Loan::with("person")
				->with("user")
				->with("fees")
				->get()
		);
	}
	public function store(Request $request)
	{
		try {
			$datos = $request->all();
			if ($datos["type"] != "Libranza") {
				if ($datos["interest_type"] == "Prestamo") {
					$dataProyeccion = LoanService::proyeccionAmortizacionAPrestamo(
						$datos["value"],
						$datos["monthly_fee"],
						$datos["interest"],
						$datos["payment_type"],
						$datos["first_payment_date"]
					);
				} else {
					$dataProyeccion = LoanService::proyeccionAmortizacion(
						$datos["value"],
						$datos["monthly_fee"],
						$datos["interest"],
						$datos["payment_type"],
						$datos["first_payment_date"]
					);
				}
			} else {
				$dataProyeccion = LoanService::proyeccionAmortizacionLibranza(
					$datos["value"],
					$datos["monthly_fee"],
					$datos["payment_type"],
					$datos["first_payment_date"]
				);
				$dataProyeccion = LoanService::proyeccionAmortizacionL(
					$datos["value"],
					$datos["monthly_fee"],
					$datos["payment_type"],
					$datos["first_payment_date"]
				);
			}
			$datos["number_fees"] =
				$datos["pay_fees"] == "No"
				? "1"
				: $dataProyeccion["number_fees"];
			$saldo = $datos["value"];

			$datos["outstanding_balance"] = $saldo;

			//if (array_key_exists('Id_Prestamo', $datos))  unset($datos['Id_Prestamo']);

			/* $oItem = new complex("Prestamo","Id_Prestamo"); 
        foreach ($datos as $index => $value) {
            $oItem->$index = $value;
        } */

			$datos["interest"] = number_format($datos["interest"], 2, ".", "");
			/*    $config = new Configuracion();
        $cod = $config->Consecutivo('Prestamo');
        $oItem->Codigo = $cod;
        unset($config);    
    
        $oItem->save();
        $id_prestamo = $oItem->getId(); */

			$datos["user_id"] = auth()->user()->id;

			$loanDB = Loan::create($datos);
			LoanService::guardarCuotasPrestamo(
				$loanDB->id,
				$dataProyeccion["proyection"]
			);
			if ($datos["type"] == "Prestamo") {
				/*
				 *CONTABILIDAD
				 $datos['Nit'] = $datos['Empleado']['Identificacion_Funcionario'];
            $datos['Id_Registro'] = $id_prestamo;
            $datos['Id_Plan_Cuenta_Banco'] = $datos['Plan_Cuenta']['Id_Plan_Cuentas'];

                $con = new ContabilidadPrestamo();
            $con->CrearMovimientoContable('Prestamo', $datos);
            unset($con); */
			}

			return $this->success("Prestamo creado");
		} catch (\Throwable $th) {
			return $this->errorResponse(
				$th->getMessage() . $th->getLine() . $th->getFile(),
				401
			);
		}
	}
}
