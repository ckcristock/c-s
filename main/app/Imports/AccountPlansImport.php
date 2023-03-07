<?php

namespace App\Imports;

use App\Models\PlanCuentas;
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
                //dd(strlen(strval($row[0])));
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
                    PlanCuentas::create([
                        'Codigo' => $row[0],
                        'Codigo_Padre' => $row[2],
                        'Nombre' => $row[1],
                        'Codigo_Niif' => $row[0],
                        'Nombre_Niif' => $row[1],
                        'Tipo_Niif' => $Tipo_P,
                        'Tipo_P' => $Tipo_P,
                        'Movimiento' => $Movimiento
                    ]);
                }
            }
        }
    }
}
