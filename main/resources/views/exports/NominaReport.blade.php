<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Nomina</title>
</head>

<body>
    <div>
        <table>
            <thead>
                <tr>
                    <th colspan="2">{{ $nomina['frecuencia_pago'] }}</th>
                    <th>{{ \Carbon\Carbon::parse(strtotime($nomina['inicio_periodo']))->toFormattedDateString()}} al</th>
                    <th>{{ \Carbon\Carbon::parse(strtotime($nomina['fin_periodo']))->toFormattedDateString()}}</th>
                </tr>
                <tr>
                    <th style="font-weight: bold;text-align: center;">Item</th>
                    <th style="font-weight: bold;text-align: center;">Nombre Empleado</th>
                    <th style="font-weight: bold;text-align: center;">C. C.</th>
                    <th style="font-weight: bold;text-align: center;">Ciudad</th>
                    <th style="font-weight: bold;text-align: center;">Cargo</th>
                    <th style="font-weight: bold;text-align: center;">Banco</th>
                    <th style="font-weight: bold;text-align: center;">Cuenta</th>
                    <th style="font-weight: bold;text-align: center;">Sueldo Basico</th>
                    <th style="font-weight: bold;text-align: center;">Dias Trabajados</th>
                    <th style="font-weight: bold;text-align: center;">Pago días trabajados</th>
                    <th style="font-weight: bold;text-align: center;">Auxilio No Salarial</th>
                    <th style="font-weight: bold;text-align: center;">Auxilio Transporte</th>
                    <th style="font-weight: bold;text-align: center;">Dias Vacaciones</th>
                    <th style="font-weight: bold;text-align: center;">Total Vacaciones</th>
                    <th style="font-weight: bold;text-align: center;">Dias Incapacidades</th>
                    <th style="font-weight: bold;text-align: center;">Total_Incapacidades</th>
                    <th style="font-weight: bold;text-align: center;">Dias Licencias</th>
                    <th style="font-weight: bold;text-align: center;">Total Licencias</th>
                    <th style="font-weight: bold;text-align: center;">Salud</th>
                    <th style="font-weight: bold;text-align: center;">Pension</th>
                    <th style="font-weight: bold;text-align: center;">Prestamos</th>
                    <th style="font-weight: bold;text-align: center;">Libranzas, Prestamos o Sanciones</th>
                    <th style="font-weight: bold;text-align: center;">Neto a Cancelar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($nomina['funcionarios'] as $key => $func)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $func['name'] }} {{ $func['surname'] }}</td>
                        <td>{{ $func['identifier'] }}</td>
                        <td>{{ $func['identifier'] }}</td>Ciudad
                        <td>{{ $func['identifier'] }}</td>Cargo
                        <td>{{ $func['identifier'] }}</td>Banco
                        <td>{{ $func['identifier'] }}</td>Cuenta
                        <td>{{ $func['identifier'] }}  | Sueldo Basico  </td>
                        <td>{{ $func['identifier'] }}  | Dias Trabajados  </td>
                        <td>{{ $func['identifier'] }}  | Pago días trabajados  </td>
                        <td>{{ $func['identifier'] }}  | Auxilio No Salarial  </td>
                        <td>{{ $func['identifier'] }}  | Auxilio Transporte  </td>
                        <td>{{ $func['identifier'] }}  | Dias Vacaciones  </td>
                        <td>{{ $func['identifier'] }}  | Total Vacaciones  </td>
                        <td>{{ $func['identifier'] }}  | Dias Incapacidades  </td>
                        <td>{{ $func['identifier'] }}  | Total_Incapacidades  </td>
                        <td>{{ $func['identifier'] }}  | Dias Licencias  </td>
                        <td>{{ $func['identifier'] }}  | Total Licencias  </td>
                        <td>{{ $func['identifier'] }}  | Salud  </td>
                        <td>{{ $func['identifier'] }}  | Pension  </td>
                        <td>{{ $func['identifier'] }}  | Prestamos  </td>
                        <td>{{ $func['identifier'] }}  | Libranzas, Prestamos o Sanciones  </td>
                        <td>={{ $func['salario_neto'] }}+{{ $func['valor_ingresos_salariales'] }}+{{ $func['valor_ingresos_no_salariales'] }}-{{ $func['valor_deducciones'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
