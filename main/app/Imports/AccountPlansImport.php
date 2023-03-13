<?php

namespace App\Imports;

use App\Models\AccountPlanBalance;
use App\Models\PlanCuentas;
use App\Models\PrettyCash;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class AccountPlansImport implements ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if ($index != 0) {
                $Tipo_P = '';
                $Movimiento = 'N';
                switch (strlen(strval($row[0]))) {
                    case 1:
                        $Tipo_P = 'CLASE';
                        break;
                    case 2:
                        $Tipo_P = 'GRUPO';
                        break;
                    case 4:
                        $Tipo_P = 'CUENTA';
                        break;
                    case 6:
                        $Tipo_P = 'SUBCUENTA';
                        break;
                    case 8:
                        $Tipo_P = 'AUXILIAR';
                        $Movimiento = 'S';
                        break;
                    default:
                        break;
                }
                $plan = PlanCuentas::where('Codigo', $row[0])->first();
                if (!$plan) {

                    $plan_cuenta= PlanCuentas::create([
                        'Codigo' => $row[0],
                        'Codigo_Padre' => $row[2],
                        'Nombre' => trim($row[1]),
                        'Codigo_Niif' => $row[0],
                        'Nombre_Niif' => trim($row[1]),
                        'Tipo_Niif' => $Tipo_P,
                        'Tipo_P' => $Tipo_P,
                        'Movimiento' => $Movimiento
                    ]);
                    AccountPlanBalance::create([
                        'account_plan_id' => $plan_cuenta->Id_Plan_Cuentas,
                        'balance' => 0
                    ]);
                    if (strval($row[2]) === '110510') {
                        PrettyCash::create([
                            'user_id' => auth()->user()->id,
                            'account_plan_id' => $plan_cuenta->Id_Plan_Cuentas,
                            'initial_balance' => 0,
                            'description' => $plan_cuenta->Nombre,
                            'status' => 'Inactiva'
                        ]);
                    }
                }
            }
        }
    }
}
