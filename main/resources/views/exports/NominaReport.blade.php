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
                    <th style="font-weight: bold;text-align: center;">Pago a Empleado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($nomina['funcionarios'] as $key => $func)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $func['name'] }} {{ $func['surname'] }}</td>
                        <td>{{ $func['identifier'] }}</td>
                        <td>={{ $func['salario_neto'] }}+{{ $func['valor_ingresos_salariales'] }}+{{ $func['valor_ingresos_no_salariales'] }}-{{ $func['valor_deducciones'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
