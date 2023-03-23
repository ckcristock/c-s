<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pago de nómina</title>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@500&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Comfortaa', cursive;
        }

        body,
        html {
            margin: 0;
            padding: 0;

        }

        .text-right {
            text-align: right !important;
        }

        .my-0 {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        figure {
            display: block;
            margin: 0 0 1rem;
        }

        .text-center {
            text-align: center !important;
        }

        .figure {
            display: inline-block;
        }

        .figure-img {
            margin-bottom: 0.5rem;
            line-height: 1;
            width: 250px;
        }

        .figure-caption {
            font-size: 90%;
            color: #6c757d;
        }

        .text-primary {
            color: #95C11F !important;
        }

        .pt-0 {
            padding-top: 0 !important;
        }

        .mr-2 {
            margin-right: 1rem !important;
        }

        .mt-2 {
            margin-top: 1rem !important;
        }

        .mt-1 {
            margin-top: 0.5rem !important;
        }

        .mt-0 {
            margin-top: 0 !important;
        }

        .mt-4 {
            margin-top: 2rem !important;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .pb-0 {
            padding-bottom: 0;
        }

        .my-0 {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        .w-100 {
            width: 100%;
        }

        .mx-4 {
            margin-left: 2rem;
            margin-right: 2rem;
        }

        .card {
            position: relative;
            display: -ms-flexbox;
            display: flex;
            background-color: #fff;
            -ms-flex-direction: column;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-clip: border-box;
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.25rem;
        }

        .card-background {
            background-color: #96c11fa5;
        }

        .rounded {
            border-radius: 1rem !important;
        }

        .mx-auto {
            margin-right: auto !important;
            margin-left: auto !important;
        }

        .col-6 {
            -ms-flex: 0 0 50%;
            flex: 0 0 50%;
            max-width: 50%;
        }

        @media (min-width: 768px) {
            .col-md-6 {
                -ms-flex: 0 0 50%;
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        .col-md-6 {
            position: relative;
            width: 100%;
            padding-left: 15px;
            padding-right: 15px;
        }

        .m-2 {
            padding: 0.5rem !important;
        }

        .card-body {
            -ms-flex: 1 1 auto;
            flex: 1 1 auto;
            min-height: 1px;
            padding: 1.25rem;
        }

        .text-white {
            color: #fff !important;
        }

        .justify-content-center {
            -ms-flex-pack: center !important;
            justify-content: center !important;
        }

        .d-flex {
            display: -ms-flexbox !important;
            display: flex !important;
        }

        .table {
            border-collapse: collapse !important;
            width: 100%;
        }

        .table td,
        .table th {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody+tbody {
            border-top: 2px solid #dee2e6;
        }

        small {
            font-size: 80%;
        }

        .small,
        small {
            font-size: 0.4em;
            font-weight: 400;
            text-align: center !important;
        }

        .svg {
            width: 100%;
            max-height: 100%;
        }
    </style>
</head>

@php
    function numberFormat($number) {
        return number_format($number, 0 ,',', '.');
    }
@endphp

<body>
    <div class="card card-background col-md-6 mx-auto rounded mt-2">
        <div class="card-body">

            <h1 class="text-center text-white my-0">{{$funcionario['name']}}</h1>{{-- {{$person->fisrt_name}} --}}
            <h2 class="text-center text-white my-0">¡Te acaban de pagar tu nómina!</h2>
            <div class="d-flex justify-content-center">
                {{--  @if ($data->genrer == 'M') --}}
                <img src="{{ asset('main/public/images/nomina.svg') }}" class="svg"> </img>
                {{-- @else
                <object data="{{ asset('main/public/images/nomina-mujer.svg') }}" class="svg"> </object>
                @endif --}}

            </div>
            <div class="card rounded">
                <div class="card-body pt-0">
                    <h6 class="text-center text-primary">El pago del día de hoy es del periodo del {{ date('d', strtotime($inicio_periodo)) }}  al {{ date('d-M-Y', strtotime($fin_periodo)) }}</h6>
                    <h4 class="text-center">PAGO TOTAL RECIBIDO</h4> {{--  --}}
                    <h1 class="text-center text-primary">${{ numberFormat($funcionario['salario_neto']) }}</h1>
                    <h4 class="text-center">Detalles de tu nómina</h4>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>Salario base</td>
                                <td class="text-right">${{ numberFormat($funcionario['retencion']['retenciones']['Salario']) }}</td>
                            </tr>
                            <tr>
                                <td>Ingresos adicionales</td>
                                <td class="text-right">{{ '$'.numberFormat($funcionario['retencion']['retenciones']['Ingresos'])}}
                                </td>
                            </tr>
                            <tr>
                                <td>Retenciones</td>
                                <td class="text-right">{{ '$'. numberFormat($funcionario['retencion']['valor_total'] + $funcionario['deducciones']['valor_total']) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <h6 class="text-center">Revisa todos los detalles en tu colilla de pago adjunta.</h6>
                    <h4 class="text-center text-primary mb-0">¡Felicitaciones!</h4>
                    <h6 class="text-center mt-0">Ya llevas {{$diff_meses}} meses en MAQMO como {{$funcionario['position']}} </h6>
                    <div class="text-center">
                        <small>Si tienes alguna inquietud, por favor contacta al administrador de pago de nómina de
                            compañía</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
