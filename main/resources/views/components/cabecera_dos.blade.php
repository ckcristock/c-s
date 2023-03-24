<head>
    <style>
        * {
            font-family: 'Roboto', sans-serif;

        }

        body,
        html {
            margin: 0;
            padding: 2rem;
            background-size: cover;
            background-color: white;
        }

        td,
        th {
            font-size: 10px;
            background-color: transparent;
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
            margin: 0 0 0rem;
        }

        .figure {
            display: inline-block;
        }

        .figure-img {
            margin-bottom: 0;
            line-height: 1;
            object-fit: cover;
            max-height: 100px;
            max-width: 100px;
        }

        .figure-caption {
            font-size: 90%;
            color: #6c757d;

        }

        .bg {
            background-color: yellow;
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
        .mt-10 {
            margin-top: 3rem !important;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .mb-1 {
            margin-bottom: 1rem;
            border-bottom: solid 1px;
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

        .my-1 {
            margin-bottom: 0.08rem;
            margin-top: 0.08rem;
        }

        .mr-2 {
            margin-right: 2rem;
        }



        .align-top {
            vertical-align: top !important;
        }

        .align-bottom {
            vertical-align: bottom !important;
        }

        /*Dulby*/
        .uppercase {
            text-transform: uppercase;
        }

        .text-center {
            text-align: center !important;
        }

        .text-left {
            text-align: left !important;
        }

        table.table-border,
        .table-border th,
        .table-border td {
            border: 1px solid;
        }

        .table-border th,
        .table-border td{
            padding: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: transparent;
        }

        .text-justify {
            text-align: justify;
            text-justify: auto;
            font-size: 9px;
        }

        .div {
            width: 100%;
            font-size: 10px;
        }

        .h-100{
            height: 100%;
        }
    </style>
</head>

<header>
    <table class="w-100">
        <tr>
            <td>
                <figure class="mx-4 align-bottom">
                    <img src="{{ $company->logo }}" class="figure-img img-fluid" height="80" />
                    <figcaption class="figure-caption">
                        <strong>{{ $company->name }}</strong> <br /><strong>NIT:</strong>
                        {{ $company->document_number }}-{{ $company->verification_digit }}
                    </figcaption>
                </figure>
            </td>
            <td class="align-bottom">
                <div class="text-right mx-4 ">
                    <h1 class="text-primary mb-0">{{ $datosCabecera->Titulo }}</h1>
                    <h2 class="my-0">{{ $datosCabecera->Codigo }}</h2>
                    <h3 class="my-0">{{ $datosCabecera->Fecha }}</h3>
                    <small>{{ $datosCabecera->CodigoFormato }}</small>
                </div>
            </td>
        </tr>
    </table>
</header>
<section class="mt-2 my-1">
