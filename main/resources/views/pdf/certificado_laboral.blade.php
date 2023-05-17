<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            font-family: 'Roboto', sans-serif;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            background-size: cover;
            background-image: url({{ $company->page_heading }});
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

        .mr-2 {
            margin-right: 1rem !important;
        }

        .mt-2 {
            margin-top: 1rem !important;
        }

        .mt-1 {
            margin-top: 0.5rem !important;
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

        .w-100 {
            width: 100%;
        }

        .mx-4 {
            margin-left: 2rem;
            margin-right: 2rem;
        }

        .m-2 {
            padding: 0.5rem !important;
        }

        .body-certificate {
            margin-left: 2rem;
            margin-right: 2rem;
            margin-top: 3rem;
            text-transform: uppercase;
        }

        footer {
            position: absolute;
            bottom: 2rem;
            width: 100%;
            margin-left: 2rem;
            margin-right: 2rem;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <header>
        <table class="w-100">
            <tr>
                <td>
                    <figure class="figure mt-4 mx-4">
                        <img src="{{ $company->logo }}" class="figure-img img-fluid" />
                        <figcaption class="figure-caption">
                            <strong>{{ $company->name }}</strong> <br /><strong>NIT:</strong>
                            {{ $company->document_number }}-{{ $company->verification_digit }}
                        </figcaption>
                    </figure>
                </td>
                <td>
                    <div class="text-right mx-4">
                        <h2 class="text-primary mb-0">Certificación laboral</h2>
                        {{-- <h2 class=my-0>{{ $datosCabecera->Codigo }}</h2>
                        <h3 class=my-0>{{ $datosCabecera->Fecha }}</h3>
                        <small>{{ $datosCabecera->CodigoFormato }}</small> --}}
                    </div>
                </td>
            </tr>
        </table>
    </header>
    <div class="body-certificate">
        <p style="font-size:16px;font-weight:bold;">
            Girón,
            {{ $date }}</p>
        <p>{{ $addressee }}</p>
        <p style="margin-top:30px; text-align: justify;">
            La empresa {{ $company->social_reason }} con
            {{ $company->document_type }} {{ $company->document_number }}-{{ $company->verification_digit }},
            {{ $gener }}
            <b>{{ $funcionario->person }}</b>
            identificado(a) con cédula de ciudadanía No.
            <b>{{ number_format($funcionario->identifier, 0, '', '.') }}</b>
            de {{ $funcionario->place_of_birth }} laboró en la empresa desde el
            {{ $date_of_admission }},
            con un contrato {{ $funcionario->contractultimate ? ($funcionario->contractultimate->work_contract_type ? $funcionario->contractultimate->work_contract_type->name : '') : '' }}

            @foreach ($informations as $info)
                @if ($info == 'salario')
                    y devengando un salario mensual de
                    {{ $salario_numeros }}
                    ($ {{ number_format(optional($funcionario->contractultimate)->salary, 0, '.', ',') }})
                @endif
                @if ($info == 'cargo')
                    desempeñando el cargo de
                    <b>{{ $funcionario->contractultimate ? ($funcionario->contractultimate->position ? $funcionario->contractultimate->position->name : '') : '' }}</b>
                @endif
            @endforeach ,
            La presente certificación se expide en Bucaramanga, al (los)
            {{ $date2->isoFormat('d') }} día(s) del mes de {{ $date2->isoFormat('MMMM') }}
            a solicitud del interesado. Y el motivo del retiro
            {{ $work_certificate->reason }}
        </p>
    </div>
    <footer>
        <p>Atentamente;</p>
        <table style="margin-top:20px">
            <tr>
                <td style="padding-top: 40px">
                    ____________________________________
                </td>
            </tr>
            <tr>
                <td style="font-weight:bold; text-align:center;">
                    ALBERTO BALCARCEL <br>
                    Director administrativo
                </td>
            </tr>
        </table>
    </footer>
</body>

</html>
