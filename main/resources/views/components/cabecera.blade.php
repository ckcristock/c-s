<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        * {
            font-family: 'Roboto', sans-serif;
            background-size: cover;
            background-image: url({{ $image }});

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

        .text-primary {
            color: #95C11F !important;
        }

        .mr-2 {
            margin-right: 1rem !important;
        }

        .mt-2 {
            margin-top: 1rem !important;
        }

        .w-100 {
            width: 100%;
        }
        .mx-4 {
            margin-left: 2rem;
            margin-right: 2rem;
        }
    </style>
</head>

<body>
    <header>
        <table class="w-100">
            <tr>
                <td>
                    <figure class="figure mt-2">
                        <img src="{{ $company->logo }}" class="figure-img img-fluid" />
                        <figcaption class="figure-caption">
                            <strong>{{ $company->name }}</strong> <br /><strong>NIT:</strong>
                            {{ $company->document_number }}-{{ $company->verification_digit }}
                        </figcaption>
                    </figure>
                </td>
                <td>
                    <div class="text-right mr-2">
                        <h4 class="text-primary mb-0">{{ $datosCabecera->Titulo }}</h4>
                        <h4 class=my-0>{{ $datosCabecera->Codigo }}</h4>
                        <h6 class=my-0>{{ $datosCabecera->Fecha }}</h6>
                        <small>{{ $datosCabecera->CodigoFormato }}</small>
                    </div>
                </td>
            </tr>
        </table>
    </header>
    <section class="mx-4">
