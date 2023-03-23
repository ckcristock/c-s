<style>
    * {
        font-family: 'Roboto', sans-serif
    }

    .page-content {
        width: 750px;
    }

    .row {
        display: inline-block;
        width: 100%;
    }

    table.table-border,
    .table-border th,
    .table-border td {
        border: 1px solid;
    }

    /*   table {
        border: 1px solid black;
    } */

    td {
        font-size: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    .section {
        padding: 10px;
        width: 100%;
        background-color: gainsboro;
    }

    .td-header {
        font-size: 10px;
        line-height: 20px;
    }

    .titular {
        /*  font-size: 11px; */
        text-transform: uppercase;
        /*   margin-bottom: 0; */
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

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center
    }

    .div {
        width: 100%;
        font-size: 10px;
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

<div class="div mt-1">
    <div class="blocks">
        <table>
            <thead>
                <tr>
                    <td>
                        <strong>Observaciones</strong>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ isset($data['observation']) ? $data['observation'] : 'No existen observaciones.' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>



@if (count($data['setpartlist']) > 0)
    <h6 class="mt-2 mb-0">LISTADO DE PIEZAS CONJUNTO</h6>
    <table class="div table-border" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="background:#E1EEC0;">
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>Costo unidario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['setpartlist'] as $setpartlist)
                <tr>
                    <td style="text-align: center;"> {{ $setpartlist['apu_type'] }} </td>
                    @if ($setpartlist['apu_type'] == 'P')
                        <td style="text-align: center;"> {{ $setpartlist['apupart']['name'] }} </td>
                    @endif
                    @if ($setpartlist['apu_type'] == 'C')
                        <td style="text-align: center;"> {{ $setpartlist['apuset']['name'] }} </td>
                    @endif
                    <td style="text-align: center;"> {{ $setpartlist['unit']['name'] }} </td>
                    <td style="text-align: center;"> {{ $setpartlist['amount'] }} </td>
                    <td style="text-align: center;"> @money($setpartlist['unit_cost']) </td>
                    <td style="text-align: right; padding-right: 5px"> @money($setpartlist['total']) </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="text-right" style="font-size: 10px;">
            <strong>SUBTOTAL: </strong>
            @money($data['list_pieces_sets_subtotal'])
        </div>
    </div>
@endif

@if (count($data['machine']) > 0)
    <h6 class="mt-1 mb-0" style="text-transform: uppercase">Máquinas herramientas</h6>
    <table class="div table-border" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="background:#E1EEC0;">
                <th>Descripción</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>Costo unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['machine'] as $machine)
                <tr>
                    <td style="text-align: center;"> {{ $machine['machine']['name'] }} </td>
                    <td style="text-align: center;"> {{ $machine['unit']['name'] }} </td>
                    <td style="text-align: center;"> {{ $machine['amount'] }} </td>
                    <td style="text-align: right; padding-right: 5px"> @money($machine['unit_cost']) </td>
                    <td style="text-align: right; padding-right: 5px"> @money($machine['total']) </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="text-right" style="font-size: 10px;">
            <strong>SUBTOTAL: </strong>
            @money($data['machine_tools_subtotal'])
        </div>
    </div>
@endif

@if (count($data['internal']) > 0)
    <h6 class="mt-1 mb-0" style="text-transform: uppercase">Procesos internos</h6>
    <table class="div table-border" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="background:#E1EEC0;">
                <th>Descripción</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>Costo unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['internal'] as $internal)
                <tr>
                    <td style="text-align: center;"> {{ $internal['internal']['name'] }} </td>
                    <td style="text-align: center;"> {{ $internal['unit']['name'] }} </td>
                    <td style="text-align: center;"> {{ $internal['amount'] }} </td>
                    <td style="text-align: right; padding-right: 5px"> @money($internal['unit_cost']) </td>
                    <td style="text-align: right; padding-right: 5px"> @money($internal['total']) </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="text-right" style="font-size: 10px;">
            <strong>SUBTOTAL: </strong>
            @money($data['internal_proccesses_subtotal'])
        </div>
    </div>
@endif

@if (count($data['external']) > 0)
    <h6 class="mt-1 mb-0" style="text-transform: uppercase">Procesos externos</h6>
    <table class="div table-border" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="background:#E1EEC0;">
                <th>Descripción</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>Costo unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['external'] as $external)
                <tr>
                    <td style="text-align: center;"> {{ $external['external']['name'] }} </td>
                    <td style="text-align: center;"> {{ $external['unit']['name'] }} </td>
                    <td style="text-align: center;"> {{ $external['amount'] }} </td>
                    <td style="text-align: right; padding-right: 5px"> @money($external['unit_cost']) </td>
                    <td style="text-align: right; padding-right: 5px"> @money($external['total']) </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="text-right" style="font-size: 10px;">
            <strong>SUBTOTAL: </strong>
            @money($data['external_proccesses_subtotal'])
        </div>
    </div>
@endif

@if (count($data['other']) > 0)
    <h6 class="mt-1 mb-0" style="text-transform: uppercase">Otros</h6>
    <table class="div table-border" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="background:#E1EEC0;">
                <th>Descripción</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>Costo unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['other'] as $other)
                <tr>
                    <td style="text-align: center;"> {{ $other['description'] }} </td>
                    <td style="text-align: center;"> {{ $other['unit']['name'] }} </td>
                    <td style="text-align: center;"> {{ $other['amount'] }} </td>
                    <td style="text-align: right; padding-right: 5px"> @money($other['unit_cost']) </td>
                    <td style="text-align: right; padding-right: 5px"> @money($other['total']) </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="text-right" style="font-size: 10px;">
            <strong>SUBTOTAL: </strong>
            @money($data['others_subtotal'])
        </div>
    </div>
@endif

<table class="mt-1">
    <tbody>
        <tr>
            <td style="vertical-align: top !important; padding-right: 20px">
                <table>
                    <thead>
                        <tr>
                            <th colspan="3" class="text-center">
                                COSTOS INDIRECTOS
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['indirect'] as $indirect)
                            <tr>
                                <td>{{ $indirect['name'] }}</td>
                                <td class="text-center">
                                    {{ $indirect['percentage'] }}%
                                </td>
                                <td class="text-right">
                                    @money($indirect['value'])
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2"><b>COSTOS INDIRECTOS</b></td>
                            <td class="text-right">@money($data['indirect_cost_total'])</td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>COSTOS DIRECTOS + COSTOS INDIRECTOS TOTALES</b></td>
                            <td class="text-right"> @money($data['direct_costs_indirect_costs_total']) </td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td style="vertical-align: top !important; padding-right: 20px">
                <table>
                    <thead>
                        <tr>
                            <th colspan="3" class="text-center">
                                AIU
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-transform: uppercase">Administrativos</td>
                            <td class="text-center">
                                {{ $data['administrative_percentage'] }}%
                            </td>
                            <td class="text-right">
                                @money($data['administrative_value'])
                            </td>
                        </tr>
                        <tr>
                            <td style="text-transform: uppercase">Imprevistos</td>
                            <td class="text-center">
                                {{ $data['unforeseen_percentage'] }}%
                            </td>
                            <td class="text-right">
                                @money($data['unforeseen_value'])
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-transform: uppercase">Subtotal + Administrativos +
                                Imprevistos</td>
                            <td class="text-right">
                                @money($data['administrative_unforeseen_subtotal'])
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-transform: uppercase">Utilidad</td>
                            <td class="text-right"> {{ $data['utility_percentage'] }}% </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-transform: uppercase">Subtotal + Admin + Imprevisto +
                                Utilidad</td>
                            <td class="text-right"> @money($data['admin_unforeseen_utility_subtotal']) </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-transform: uppercase">Precio venta total COP + Retención
                            </td>
                            <td class="text-right"> @money($data['sale_price_cop_withholding_total'])</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-transform: uppercase">TRM</td>
                            <td class="text-right"> @money($data['trm']) </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-transform: uppercase">Precio venta total USD + Retención
                            </td>
                            <td class="text-right"> USD @money($data['sale_price_usd_withholding_total']) </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
