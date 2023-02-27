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
        foreach ($rows as $index=>$row)
        {
            if ($index != 0){
                PlanCuentas::create([
                    'Codigo' => $row[0],
                    'Nombre' => $row[1],
                    'Codigo_Niif' => $row[0],
                    'Nombre_Niif' => $row[1],
                ]);
            }
        }
    }

}
