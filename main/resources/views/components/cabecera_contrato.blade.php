<style>
    * {
        font-family: 'Roboto', sans-serif
    }

    body,
    html {
        margin: 0;
        width: 100%;
        padding: 0;
        background-size: cover;
        background-image: url({{ $person->work_contract->company->page_heading }});
    }

    body {
        margin-top: 2rem;
    }

    .table-bordered {
        border: 1px solid #dee2e6;
    }

    .table-bordered {
        border: 1px solid #dee2e6;
    }

    .table-bordered td,
    .table-bordered th {
        border: 1px solid #dee2e6;
    }

    .table-bordered thead td,
    .table-bordered thead th {
        border-bottom-width: 2px;
    }

    .cuerpo-contrato {
        padding-left: 3rem;
        padding-right: 3rem;
    }

    .text-center {
        text-align: center
    }

    .texto-contrato {
        text-align: justify;
    }

    table {
        border-collapse: collapse;
    }

    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
    }

    .table td,
    .table th {
        padding: 0.5rem;
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
</style>

<table class="table table-bordered" style="margin-top: 30px; margin-bottom: 30px; text-transform: uppercase;">
    <tbody>
        <tr>
            <td colspan="2" style="text-align: center">
                <strong><em>EL EMPLEADOR</em></strong>
            </td>
        </tr>
        <tr>
            <td style="width: 350px">El Empleador:</td>
            <td>{{ $person->work_contract->company->social_reason }}</td>
        </tr>
        <tr>
            <td>NIT:</td>
            <td>{{ $person->work_contract->company->document_number . '-' . $person->work_contract->company->verification_digit }}
            </td>
        </tr>
        <tr>
            <td>Dirección Principal:</td>
            <td>CRA 16 # 60-14 La Esmeralda</td>
        </tr>
        <tr>
            <td>Ciudad:</td>
            <td>Girón (Santander)</td>
        </tr>
        <tr>
            <td>Representante Legal:</td>
            <td>ALBERTO BALCARCEL ZAMBRANO</td>
        </tr>
        <tr>
            <td>Cédula:</td>
            <td>13.835.833</td>
        </tr>
        <tr>
            <td style="text-align: center" colspan="2">
                <strong><em>EL TRABAJADOR</em></strong>
            </td>
        </tr>
        <tr>
            <td>El Trabajador:</td>
            <td>{{ $person->person }}</td>
        </tr>
        <tr>
            <td>Cédula:</td>
            <td>{{ number_format($person->identifier, 0, '', '.') }}</td>
        </tr>
        <tr>
            <td>Dirección:</td>
            <td>{{ $person->direction }}</td>
        </tr>
        <tr>
            <td>Ciudad:</td>
            <td>{{ $person->place_of_birth }}</td>
        </tr>
        <tr>
            <td>Teléfono:</td>
            <td>{{ $person->cell_phone }}</td>
        </tr>
        <tr>
            <td>Correo electrónico:</td>
            <td>{{ $person->email }}</td>
        </tr>
        <tr>
            <td colspan="2" valign="bottom" style="text-align: center">
                <strong><em>DEL CONTRATO</em></strong>
            </td>
        </tr>
        <tr>
            <td>Cargo contratado:</td>
            <td valign="bottom">{{ $person->contractultimate->position->name }}</td>
        </tr>
        <tr>
            <td>Salario mensual:</td>
            <td valign="bottom">
                ${{ number_format($person->contractultimate->salary, 0, '', '.') . ' (' . $construct_dates->enLetras . ' MCTE)' }}
            </td>
        </tr>
        <tr>
            <td>Forma de pago:</td>
            <td valign="bottom">{{ $person->work_contract->company->payment_frequency }}</td>
        </tr>
        <tr>
            <td>Fecha de Inicio:</td>
            <td valign="bottom">{{ $construct_dates->fechaInicio }}</td>
        </tr>
        @if ($person->contractultimate->work_contract_type->name == 'Fijo')
            <tr>
                <td>Duración:</td>
                <td valign="bottom">{{ $construct_dates->diferencia->format('%m') }} meses</td>
            </tr>
            <tr>
                <td>Fecha de Terminación:</td>
                <td valign="bottom">{{ $construct_dates->fechaFin }}</td>
            </tr>
        @endif

        @if ($person->contractultimate->work_contract_type->name == 'Obra/Labor')
            <tr>
                <td>Fecha de Terminación:</td>
                <td valign="bottom">A LA TERMINACIÓN TOTAL O PARCIAL DE LA OBRA O LABOR
                    CONTRATADA</td>
            </tr>
            <tr>
                <td>Fecha de Suscripción:</td>
                <td>{{ $construct_dates->fechaCreado }}</td>
            </tr>
        @endif
        @if (
            $person->contractultimate->work_contract_type->name == 'Fijo' ||
                $person->contractultimate->work_contract_type->name == 'Indefinido')
            <tr>
                <td>PERIODO DE PRUEBA:</td>
                <td valign="bottom">{{ $construct_dates->prueba }} DIAS</td>
            </tr>
            <tr>
                <td>FECHA DE PERIODO DE PRUEBA:</td>
                <td valign="bottom">
                    {{ $construct_dates->prueba . ' DIAS (' . $construct_dates->fechaInicio . ' AL ' . $construct_dates->fechaFinPrueba . ')' }}
                </td>
            </tr>
            <tr>
                <td>Lugar de la prestación del servicio:</td>
                <td valign="bottom">GIRÓN (SANTANDER)</td>
            </tr>
        @endif
        @if (
            $person->contractultimate->work_contract_type->name == 'Obra/Labor' ||
                $person->contractultimate->work_contract_type->name == 'Indefinido')
            <tr>
                <td>Auxilio de transporte:</td>
                <td valign="bottom">$
                    {{ number_format($person->work_contract->company->transportation_assistance, 0, '', '.') }}
                </td>
            </tr>
        @endif
    </tbody>
</table>
