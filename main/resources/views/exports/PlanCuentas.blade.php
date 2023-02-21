<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PlanCuentas</title>
    <style>
        .title {
            font-weight: bold;
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
    </style>
</head>
@php
    function TransformarValor($value)
    {
        if ($value == 'N' || $value == '' || is_null($value)) {
            return 'NO';
        } else {
            return 'SI';
        }
    }
@endphp

<body>
    <table>
        <thead>
            <tr class="title">
                <th>Tipo</th>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Ajuste contable</th>
                <th>¿Cierra Terceros?</th>
                <th>Movimiento</th>
                <th>Documento</th>
                <th>Base</th>
                <th>Valor</th>
                <th>Porcentaje</th>
                <th>Centro de costo</th>
                <th>Depreciacion</th>
                <th>Amortizacion</th>
                <th>Exogeno</th>
                <th>Naturaleza</th>
                <th>¿Maneja NIT?</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($planes as $key => $plan)
                <tr>
                    <td>{{ $plan['Tipo_Niif'] }}</td>
                    <td class="text-left">{{ $plan['Codigo_Niif'] }}</td>
                    <td>{{ $plan['Nombre_Niif'] }}</td>
                    <td>{{ $plan['Estado'] }}</td>
                    <td>{{ TransformarValor($plan['Ajuste_Contable']) }}</td>
                    <td>{{ TransformarValor($plan['Cierra_Terceros']) }}</td>
                    <td>{{ TransformarValor($plan['Movimiento']) }}</td>
                    <td>{{ TransformarValor($plan['Documento']) }}</td>
                    <td>{{ TransformarValor($plan['Base']) }}</td>
                    <td>{{ $plan['Valor'] }}</td>
                    <td>{{ $plan['Porcentaje'] }}</td>
                    <td>{{ TransformarValor($plan['Centro_Costo']) }}</td>
                    <td>{{ TransformarValor($plan['Depreciacion']) }}</td>
                    <td>{{ TransformarValor($plan['Amortizacion']) }}</td>
                    <td>{{ TransformarValor($plan['Exogeno']) }}</td>
                    <td>{{ $plan['Naturaleza'] }}</td>
                    <td>{{ TransformarValor($plan['Maneja_Nit']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
