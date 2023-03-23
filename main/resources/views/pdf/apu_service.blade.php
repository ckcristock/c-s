<style>
    .page-content {
        width: 750px;
    }

    .row {
        display: inline-block;
        width: 100%;
    }

    td {
        font-size: 10px;
        background-color: transparent;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: transparent;
    }

    .blocks {
        width: 25%;
        display: inline-block;
        text-transform: uppercase;
    }

    .blocks-50 {
        width: 50%;
        display: inline-block;
        text-transform: uppercase;
    }

    .text-center {
        text-align: center
    }

    .div {
        width: 100%;
        font-size: 9px;
    }

    table.table-border,
    .table-border th,
    .table-border td {
        border: 1px solid;
    }

    .text-right {
        text-align: right;
    }

    .text-left {
        text-align: left !important;
    }
</style>
@include('components/cabecera', [$company, $datosCabecera, $image])
<div class="div">
    <div class="blocks-50">
        <strong>Nombre:</strong>
        {{ $data['name'] }}
    </div>
    <div class="blocks">
        <strong>Cliente:</strong>
        {{ $data['thirdparty']['name'] }}
    </div>
    <div class="blocks">
        <strong>Destino:</strong>
        {{ $data['city']['name'] }}
    </div>
</div>
<div class="div">
    <div class="blocks-50">
        <strong>Quién elabora:</strong>
        {{ $data['person']['name'] }}
    </div>

    <div class="blocks">
        <strong>Línea:</strong>
        {{ $data['line'] }}
    </div>
</div>
<table class="div mt-1">
    <tbody>
        <tr>
            <td><strong>OBSERVACIONES</strong></td>
        </tr>
        <tr>
            <td>
                {{ isset($data['observation']) ? $data['observation'] : 'No existen observaciones.' }}
        </tr>
    </tbody>
</table>

@if (count($data['dimensionalValidation']) > 0)
    <h5 class="mt-2 text-center">VALIDACIÓN DIMENSIONAL</h5>
    <h6>CÁLCULO DE MANO DE OBRA</h6>
    <table class="div table-border">
        <thead>
            <tr class="table-primary" style="background:#E1EEC0;">
                <th colspan="3" class="text-center">DATOS</th>
                <th colspan="5" class="text-center">DESPLAZAMIENTO</th>
                <th colspan="5" class="text-center">ORDINARIAS</th>
                <th colspan="6" class="text-center">FESTIVAS</th>
            </tr>
        </thead>
        <thead class="bg-light">
            <tr class="text-center text-uppercase" style="background:#E1EEC0;">
                <th class="align-middle">Perfil</th>
                <th class="align-middle">Tipo Desplaza</th>
                <th class="align-middle">N. Persona</th>
                <th class="align-middle">N. Días</th>
                <th class="align-middle">Jornada</th>
                <th class="align-middle">Horas</th>
                <th class="align-middle">Valor Horas</th>
                <th class="align-middle">Valor Total</th>
                <th class="align-middle">N. Días</th>
                <th class="align-middle">Jornada</th>
                <th class="align-middle">Horas</th>
                <th class="align-middle">Valor Hora</th>
                <th class="align-middle">Valor Total</th>
                <th class="align-middle">N. Días</th>
                <th class="align-middle">Jornada</th>
                <th class="align-middle">Horas</th>
                <th class="align-middle">Valor Hora</th>
                <th class="align-middle">Valor Total</th>
                <th class="align-middle">Valor Salarial</th>
            </tr>
        </thead>
        @foreach ($data['dimensionalValidation'] as $key => $dimentional)
            <tbody>
                <tr class="text-center">
                    <td class="align-middle">
                        {{ $dimentional['profiles']['profile'] }}
                    </td>
                    <td class="align-middle">
                        {{ $dimentional['displacement_type'] }}
                    </td>
                    <td class="align-middle">
                        {{ $dimentional['people_number'] }}
                    </td>
                    <td class="align-middle">
                        {{ $dimentional['days_number_displacement'] }}
                    </td>
                    <td class="align-middle">
                        {{ $dimentional['workind_day_displacement'] }}
                    </td>
                    <td class="align-middle">
                        {{ $dimentional['hours_displacement'] }}
                    </td>
                    <td class="align-middle">
                        @money($dimentional['hours_value_displacement'])
                    </td>
                    <td class="align-middle">
                        @money($dimentional['total_value_displacement'])
                    </td>
                    <td class="align-middle">
                        {{ $dimentional['days_number_ordinary'] }}
                    </td>
                    <td class="align-middle">
                        {{ $dimentional['working_day_ordinary'] }}
                    </td>
                    <td class="align-middle">
                        {{ $dimentional['hours_ordinary'] }}
                    </td>
                    <td class="align-middle">
                        @money($dimentional['hours_value_ordinary'])
                    </td>
                    <td class="align-middle">
                        @money($dimentional['total_value_ordinary'])
                    </td>
                    <td class="align-middle">
                        {{ $dimentional['days_number_festive'] }}
                    </td>
                    <td class="align-middle">
                        {{ $dimentional['working_day_festive'] }}
                    </td>
                    <td class="align-middle">
                        {{ $dimentional['hours_festive'] }}
                    </td>
                    <td class="align-middle">
                        @money($dimentional['hours_value_festive'])
                    </td>
                    <td class="align-middle">
                        @money($dimentional['total_value_festive'])
                    </td>
                    <td class="align-middle">
                        @money($dimentional['salary_value'])
                    </td>
                </tr>
            </tbody>
        @endforeach
    </table>
    <div class="row">
        <div class="text-right" style="font-size: 10px;">
            <strong>SUBTOTAL MANO DE OBRA: </strong>
            @money($data['subtotal_labor'])
        </div>
        <div class="text-right" style="font-size: 10px;">
            <strong>SUBTOTAL VIÁTICOS: </strong>
            @money($data['subtotal_travel_expense'])
        </div>
        <div class="text-right" style="font-size: 10px;">
            <strong>SUBTOTAL VALID DIMENSIONAL: </strong>
            @money($data['subtotal_dimensional_validation'])
        </div>
    </div>
@endif



@if (count($data['assembliesStartUp']) > 0)
    <hr />
    <h5 class="text-center">MONTAJE Y PUESTA EN MARCHA</h5>
    <h6>CÁLCULO DE MANO DE OBRA</h6>
    <table class="div table-border">
        <thead>
            <tr style="background:#E1EEC0;">
                <th colspan="4">DATOS</th>
                <th colspan="5" class="text-center">DESPLAZAMIENTO</th>
                <th colspan="5" class="text-center">ORDINARIAS</th>
                <th colspan="6" class="text-center">FESTIVAS</th>
            </tr>
        </thead>
        <thead class="bg-light">
            <tr class="text-center text-uppercase">
                <th class="align-middle">Item</th>
                <th class="align-middle">Perfil</th>
                <th class="align-middle">Tipo Desplaza</th>
                <th class="align-middle">N. Persona</th>
                <th class="align-middle">N. Días</th>
                <th class="align-middle">Jornada</th>
                <th class="align-middle">Horas</th>
                <th class="align-middle">Valor Horas</th>
                <th class="align-middle">Valor Total</th>
                <th class="align-middle">N. Días</th>
                <th class="align-middle">Jornada</th>
                <th class="align-middle">Horas</th>
                <th class="align-middle">Valor Hora</th>
                <th class="align-middle">Valor Total</th>
                <th class="align-middle">N. Días</th>
                <th class="align-middle">Jornada</th>
                <th class="align-middle">Horas</th>
                <th class="align-middle">Valor Hora</th>
                <th class="align-middle">Valor Total</th>
                <th class="align-middle">Valor Salarial</th>
            </tr>
        </thead>
        @foreach ($data['assembliesStartUp'] as $key => $assemblies)
            <tbody>
                <tr class="text-center">
                    <td class="align-middle">
                        {{ $key + 1 }}
                    </td>
                    <td class="align-middle" *ngIf="item.profiles">
                        {{ $assemblies['profiles']['profile'] }}
                    </td>
                    <td class="align-middle">
                        {{ $assemblies['displacement_type'] }}
                    </td>
                    <td class="align-middle">
                        {{ $assemblies['people_number'] }}
                    </td>
                    <td class="align-middle">
                        {{ $assemblies['days_number_displacement'] }}
                    </td>
                    <td class="align-middle">
                        {{ $assemblies['workind_day_displacement'] }}
                    </td>
                    <td class="align-middle">
                        {{ $assemblies['hours_displacement'] }}
                    </td>
                    <td class="align-middle">
                        @money($assemblies['hours_value_displacement'])
                    </td>
                    <td class="align-middle">
                        @money($assemblies['total_value_displacement'])
                    </td>
                    <td class="align-middle">
                        {{ $assemblies['days_number_ordinary'] }}
                    </td>
                    <td class="align-middle">
                        {{ $assemblies['working_day_ordinary'] }}
                    </td>
                    <td class="align-middle">
                        {{ $assemblies['hours_ordinary'] }}
                    </td>
                    <td class="align-middle">
                        @money($assemblies['hours_value_ordinary'])
                    </td>
                    <td class="align-middle">
                        @money($assemblies['total_value_ordinary'])
                    </td>
                    <td class="align-middle">
                        {{ $assemblies['days_number_festive'] }}
                    </td>
                    <td class="align-middle">
                        {{ $assemblies['working_day_festive'] }}
                    </td>
                    <td class="align-middle">
                        {{ $assemblies['hours_festive'] }}
                    </td>
                    <td class="align-middle">
                        @money($assemblies['hours_value_festive'])
                    </td>
                    <td class="align-middle">
                        @money($assemblies['total_value_festive'])
                    </td>
                    <td class="align-middle">
                        @money($assemblies['salary_value'])
                    </td>
                </tr>
            </tbody>
        @endforeach
    </table>
    <div class="row">
        <div class="text-right" style="font-size: 10px;">
            <strong>SUBTOTAL MANO DE OBRA: </strong>
            @money($data['subtotal_labor_mpm'])
        </div>
        <div class="text-right" style="font-size: 10px;">
            <strong>SUBTOTAL VIÁTICOS: </strong>
            @money($data['subtotal_travel_expense_mpm'])
        </div>
        <div class="text-right" style="font-size: 10px;">
            <strong>SUBTOTAL MONTAJE Y PUESTA EN MARCHA: </strong>
            @money($data['subtotal_assembly_commissioning'])
        </div>
        <div class="text-right" style="font-size: 10px;">
            <strong>SUBTOTAL GENERAL VIÁTICOS + MANO DE OBRA: </strong>
            @money($data['general_subtotal_travel_expense_labor'])
        </div>
    </div>
@endif
<hr />
<h5 class="text-center">AIU</h5>
<table class="table-border" style="font-size: 15px; text-transform: uppercase">
    <tbody>
        <tr class="text-right">
            <td class="text-left">Administrativos</td>
            <td>{{ $data['administrative_percentage'] }}%</td>
            <td>@money($data['administrative_value'])</td>
        </tr>
        <tr class="text-right">
            <td class="text-left">Imprevistos</td>
            <td>{{ $data['unforeseen_percentage'] }}%</td>
            <td>@money($data['unforeseen_value'])</td>
        </tr>
        <tr class="text-right">
            <td class="text-left" colspan="2">Subtotal A + I</td>
            <td>
                @money($data['subtotal_administrative_unforeseen'])
            </td>
        </tr>
        <tr class="text-right">
            <td class="text-left" colspan="2">Utilidad</td>
            <td>{{ $data['utility_percentage'] }}%</td>
        </tr>
        <tr class="text-right">
            <td class="text-left" colspan="2">SubTotal A + I + U</td>
            <td>
                @money($data['subtotal_administrative_unforeseen_utility'])
            </td>
        </tr>
        <tr class="text-right">
            <td class="text-left" colspan="2">
                Precio venta COP incluye retención
            </td>
            <td>
                @money($data['sale_price_cop_withholding_total'])
            </td>
        </tr>
        <tr class="text-right">
            <td class="text-left" colspan="2">TRM</td>
            <td>@money($data['trm'])</td>
        </tr>
        <tr class="text-right">
            <td class="text-left" colspan="2">
                Precio venta USD incluye retención
            </td>
            <td>USD @money($data['sale_price_usd_withholding_total'])</td>
        </tr>
    </tbody>
</table>
