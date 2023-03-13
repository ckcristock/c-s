<head>
    <style>
        * {
            font-family: 'Roboto', sans-serif;
            font-size: xx-small;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            background-size: cover;
            background-image: url({{ $image }});
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
            object-fit: cover;
            max-height: 100px;
            max-width: 100px;
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
        .uppercase {
            text-transform: uppercase;
        }

        .text-center {
            text-align: center !important;
        }
        .text-left {
            text-align: left !important;
        }
    </style>
</head>

<header>
    <table class="w-100">
        <tr>
            <td>
                <figure class="figure mt-4 mx-4">
                    <img src="{{ $company->logo }}" class="figure-img img-fluid" height="60" />
                    <figcaption class="figure-caption">
                        <strong>{{ $company->name }}</strong> <br /><strong>NIT:</strong>
                        {{ $company->document_number }}-{{ $company->verification_digit }}
                    </figcaption>
                </figure>
            </td>
            <td>
                <div class="text-right mx-4">
                    <h1 class="text-primary mb-0">{{ $datosCabecera->Titulo }}</h1>
                    <h2 class=my-0>{{ $datosCabecera->Codigo }}</h2>
                    <h3 class=my-0>{{ $datosCabecera->Fecha }}</h3>
                    <small>{{ $datosCabecera->CodigoFormato }}</small>
                </div>
            </td>
        </tr>
    </table>
</header>
<section class="mx-4">
