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
                    <th
                        style="font-weight: bold;
                               font-size: 14px;
                               text-align: center;"
                        colspan="4"
                    >RESUMEN DE NOMINA {{$nomina->frecuencia_pago}}</th>
                </tr>
                <tr>
                    <th style="font-weight: bold;text-align: center;">Item</th>
                    <th style="font-weight: bold;text-align: center;">Nombre Empleado</th>
                    <th style="font-weight: bold;text-align: center;">C. C.</th>
                    <th style="font-weight: bold;text-align: center;">Ciudad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nomina->funcionarios as $key => $func)
                <tr>
                    <td>{{$key + 1 }}</td>
                    <td>{{$func->name}}  {{$func->surname}}</td>
                    <td>{{$func->identifier}}</td>
                    <td>{{$func->salario_neto }}</td>
                </tr>
                @endforeach
{{--                 <tr>
                    <td colspan="5" style="text-align: right;"><B>TOTAL</B></td>
                    <td>=SUM(F3:F{{$bonuses->total_employees+2}})</td>
                </tr> --}}

                @foreach($nomina['funcionarios'] as $key => $func)
                <tr>
                    <td>{{$key + 1 }}</td>
                    <td>{{$func['name']}}  {{$func['surname']}}</td>
                    <td>{{$func['identifier']}}</td>
                    <td>{{$func['salario_neto'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>

{{--
    "id": 1,
    "user_id": null,
    "code": null,
    "payment_frequency": "Mensual",
    "start_period": "2021-12-01",
    "end_period": "2021-12-31",
    "total_salaries": 63437825,
    "total_retentions": 6273940,
    "total_provisions": 15220379,
    "total_social_secturity": 15335079,
    "total_parafiscals": 6225470,
    "total_overtimes_surcharges": 0,
    "total_incomes": 1000,
    "total_cost": 106492693,
    "created_at": "2021-12-16T11:26:36.000000Z",
    "updated_at": "2021-12-16T11:26:36.000000Z",
    "electronic_reported": null,
    "electronic_reported_date": null,
    "user_electronic_reported": null,
    "company_id": null
 --}}
