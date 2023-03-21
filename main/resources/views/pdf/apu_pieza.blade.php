<style>
    .page-content {
        width: 750px;
    }

    td.text-right {
        padding-right: 5px;
    }

    .row {
        display: inline-block;
        width: 100%;
    }

    td {
        font-size: 10px;
        background-color: transparent;
    }

    table.table-border,
    .table-border th,
    .table-border td {
        border: 1px solid;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: transparent;
    }

    .section {
        padding: 10px;
        width: 100%;
        background-color: transparent;
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
        {{ $data['person']['first_name'] }} {{ $data['person']['first_surname'] }}
    </div>

    <div class="blocks">
        <strong>Línea:</strong>
        {{ $data['line'] }}
    </div>
    <div class="blocks">
        <strong>Cantidad:</strong>
        {{ $data['amount'] }}
    </div>
</div>

<div class="div">
    <div class="blocks" class="div">
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



@if (count($data['rawmaterial']) > 0)
    <h6 class="mt-2 mb-0">CÁLCULO DE MATERIA PRIMA</h6>
    <table class="div table-border" cellpadding="0" cellspacing="0">
        @foreach ($data['rawmaterial'] as $rawmaterial)
            <thead>
                <tr style="background:#E1EEC0;">
                    <th>Geometría</th>
                    <th>Material</th>
                    @foreach ($rawmaterial['measures'] as $measures)
                        <th> {{ $measures['name'] }} </th>
                    @endforeach
                    <th>Peso KG</th>
                    <th>Cantidad</th>
                    <th>Peso total</th>
                    <th>Valor KG</th>
                    <th>Valor total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;""> {{ $rawmaterial['geometry']['name'] }} </td>
                    <td style="text-align: center;"> {{ $rawmaterial['material']['product']['name'] }} </td>
                    @foreach ($rawmaterial['measures'] as $measures)
                        <td style="text-align: center;"> {{ $measures['value'] }} </td>
                    @endforeach
                    <td style="text-align: center;"> {{ $rawmaterial['weight_kg'] }} </td>
                    <td style="text-align: center;"> {{ $rawmaterial['q'] }} </td>
                    <td style="text-align: center;"> {{ $rawmaterial['weight_total'] }} </td>
                    <td style="text-align: right; padding-right: 5px"> @money($rawmaterial['value_kg'])</td>
                    <td style="text-align: right; padding-right: 5px"> @money($rawmaterial['total_value']) </td>
                </tr>
            </tbody>
        @endforeach
    </table>
    <div class="row">
        <div class="text-right mb-0 pb-0" style="font-size: 10px;">
            <strong>SUBTOTAL: </strong>
            @money($data['subtotal_raw_material'])
        </div>
    </div>
@endif


@if (count($data['commercial']) > 0)
    <h6 class="mt-1 mb-0">MATERIALES COMERCIALES</h6>
    <table class="div table-border" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="background:#E1EEC0;">
                <th>Material</th>
                <th>Unidad</th>
                <th>Cant. unitaria</th>
                <th>Cant. total</th>
                <th>Costo unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['commercial'] as $commercial)
                <tr>
                    <td style="text-align: center;"> {{ $commercial['material']['name'] }} </td>
                    <td style="text-align: center;"> {{ $commercial['unit']['name'] }} </td>
                    <td style="text-align: center;"> {{ $commercial['q_unit'] }} </td>
                    <td style="text-align: center;"> {{ $commercial['q_total'] }} </td>
                    <td style="text-align: right; padding-right: 5px"> @money($commercial['unit_cost']) </td>
                    <td style="text-align: right; padding-right: 5px"> @money($commercial['total']) </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="text-right" style="font-size: 10px;">
            <strong>SUBTOTAL: </strong>
            @money($data['commercial_materials_subtotal'])
        </div>
    </div>
@endif


@if (count($data['cutwater']) > 0)
    <h6 class="mt-1 mb-0">CORTE AGUA</h6>
    <table class="div table-border" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="background:#E1EEC0;">
                <th>Material</th>
                <th>Espesor(mm)</th>
                <th>Cantidad</th>
                <th>Largo(mm)</th>
                <th>Ancho(mm)</th>
                <th>Longitud total</th>
                <th>Cantidad</th>
                <th>Diámetro(mm)</th>
                <th>Perim total agujero</th>
                <th>Tiempo(min)</th>
                <th>Valor minuto</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['cutwater'] as $cutwater)
                <tr>
                    <td style="text-align: center;"> {{ $cutwater['material']['product']['name'] }} </td>
                    <td style="text-align: center;"> {{ $cutwater['thickness']['thickness'] }} </td>
                    <td style="text-align: center;"> {{ $cutwater['amount'] }} </td>
                    <td style="text-align: center;"> {{ $cutwater['long'] }} </td>
                    <td style="text-align: center;"> {{ $cutwater['width'] }} </td>
                    <td style="text-align: center;"> {{ $cutwater['total_length'] }} </td>
                    <td style="text-align: center;"> {{ $cutwater['amount_cut'] }} </td>
                    <td style="text-align: center;"> {{ $cutwater['diameter'] }} </td>
                    <td style="text-align: center;"> {{ $cutwater['total_hole_perimeter'] }} </td>
                    <td style="text-align: center;"> {{ $cutwater['time'] }} </td>
                    <td style="text-align: right; padding-right: 5px"> @money($cutwater['minute_value']) </td>
                    <td style="text-align: right; padding-right: 5px"> @money($cutwater['value']) </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="text-right" style="font-size: 10px;">
            <strong>CANTIDAD TOTAL: </strong>
            {{ $data['cut_water_total_amount'] }}
        </div>
        <div class="text-right" style="font-size: 10px;">
            <strong>SUBTOTAL UNITARIO: </strong>
            @money($data['cut_water_unit_subtotal'])
        </div>
        <div class="text-right" style="font-size: 10px;">
            <strong>SUBTOTAL: </strong>
            @money($data['cut_water_subtotal'])
        </div>
    </div>
@endif


@if (count($data['cutlaser']) > 0)
    <h6 class="mt-1 mb-0">CORTE LÁSER</h6>
    <table class="div table-border" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="background:#E1EEC0;">
                <th>Material</th>
                <th>Espesor(mm)</th>
                <th>Cantidad láminas</th>
                <th>Largo(mm)</th>
                <th>Ancho(mm)</th>
                <th>Longitud total</th>
                <th>Cant. agujeros</th>
                <th>Diámetro(mm)</th>
                <th>Perim total agujero</th>
                <th>Tiempo(min)</th>
                <th>Valor minuto</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['cutlaser'] as $cutlaser)
                <tr>
                    <td style="text-align: center;">{{ $cutlaser['cutLaserMaterial']['product']['name'] }}</td>
                    <td style="text-align: center;"> {{ $cutlaser['thickness'] }} </td>
                    <td style="text-align: center;"> {{ $cutlaser['sheets_amount'] }} </td>
                    <td style="text-align: center;"> {{ $cutlaser['long'] }} </td>
                    <td style="text-align: center;"> {{ $cutlaser['width'] }} </td>
                    <td style="text-align: center;"> {{ $cutlaser['total_length'] }} </td>
                    <td style="text-align: center;"> {{ $cutlaser['amount_holes'] }} </td>
                    <td style="text-align: center;"> {{ $cutlaser['diameter'] }} </td>
                    <td style="text-align: center;"> {{ $cutlaser['total_hole_perimeter'] }} </td>
                    <td style="text-align: center;"> {{ $cutlaser['time'] }} </td>
                    <td style="text-align: right; padding-right: 5px"> @money($cutlaser['minute_value']) </td>
                    <td style="text-align: right; padding-right: 5px"> @money($cutlaser['value']) </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="text-right pb-0 mb-0" style="font-size: 10px;">
            <strong>CANTIDAD TOTAL: </strong>
            {{ $data['cut_laser_total_amount'] }}
        </div>
        <div class="text-right pb-0 mb-0" style="font-size: 10px;">
            <strong>SUBTOTAL UNITARIO: </strong>
            @money($data['cut_laser_unit_subtotal'])
        </div>
        <div class="text-right pb-0 mb-0" style="font-size: 10px;">
            <strong>SUBTOTAL: </strong>
            @money($data['cut_laser_subtotal'])
        </div>
    </div>
@endif

@if (count($data['machine']) > 0)
    <h6 class="mt-1 mb-0">MÁQUINAS HERRAMIENTAS</h6>
    <table class="div table-border" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="background:#E1EEC0;">
                <th>Descripción</th>
                <th>Unidad</th>
                <th>Cant. unitaria</th>
                <th>Cant. total</th>
                <th>Costo unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['machine'] as $machine)
                <tr>
                    <td style="text-align: center;"> {{ $machine['machine']['name'] }} </td>
                    <td style="text-align: center;"> {{ $machine['unit']['name'] }} </td>
                    <td style="text-align: center;"> {{ $machine['q_unit'] }} </td>
                    <td style="text-align: center;"> {{ $machine['q_total'] }} </td>
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
    <h6 class="mt-1 mb-0">PROCESOS INTERNOS</h6>
    <table class="div table-border" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="background:#E1EEC0;">
                <th>Descripción</th>
                <th>Unidad</th>
                <th>Cant. unitaria</th>
                <th>Cant. total</th>
                <th>Costo unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['internal'] as $internal)
                <tr>
                    <td style="text-align: center;"> {{ $internal['internal']['name'] }} </td>
                    <td style="text-align: center;"> {{ $internal['unit']['name'] }} </td>
                    <td style="text-align: center;"> {{ $internal['q_unit'] }} </td>
                    <td style="text-align: center;"> {{ $internal['q_total'] }} </td>
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
    <h6 class="mt-1 mb-0">PROCESOS EXTERNOS</h6>
    <table class="div table-border" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="background:#E1EEC0;">
                <th>Descripción</th>
                <th>Unidad</th>
                <th>Cant. unitaria</th>
                <th>Cant. total</th>
                <th>Costo unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['external'] as $external)
                <tr>
                    <td style="text-align: center;"> {{ $external['external']['name'] }} </td>
                    <td style="text-align: center;"> {{ $external['unit']['name'] }} </td>
                    <td style="text-align: center;"> {{ $external['q_unit'] }} </td>
                    <td style="text-align: center;"> {{ $external['q_total'] }} </td>
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
    <h6 class="mt-1 mb-0">OTROS</h6>
    <table class="div table-border" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="background:#E1EEC0;">
                <th>Descripción</th>
                <th>Unidad</th>
                <th>Cant. unitaria</th>
                <th>Cant. total</th>
                <th>Costo unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['other'] as $other)
                <tr>
                    <td style="text-align: center;"> {{ $other['description'] }} </td>
                    <td style="text-align: center;"> {{ $other['unit']['name'] }} </td>
                    <td style="text-align: center;"> {{ $other['q_unit'] }} </td>
                    <td style="text-align: center;"> {{ $other['q_total'] }} </td>
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

<table class="div mt-1">
    <thead>
        <tr style="background:#E1EEC0;">
            <th>COSTO DIRECTO UNITARIO</th>
            <th>COSTO DIRECTO TOTAL</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: center;">
                @money($data['unit_direct_cost'])
            </td>
            <td style="text-align: center;">
                @money($data['total_direct_cost'])
            </td>
        </tr>
    </tbody>
</table>

<table class="mt-1">
    <tbody>
        <tr>
            <td style="vertical-align: top !important; padding-right: 20px">
                <table>
                    <thead>
                        <tr>
                            <th class="text-center" colspan="3">
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
                    </tbody>
                </table>
                <table>
                    <tbody>
                        <tr>
                            <td><b>COSTOS DIRECTOS + COSTOS INDIRECTOS TOTALES</b></td>
                            <td class="text-right"> @money($data['direct_costs_indirect_costs_total'])</td>
                        </tr>
                        <tr>
                            <td><b>COSTOS DIRECTOS + COSTOS INDIRECTOS UNITARIO</b></td>
                            <td class="text-right"> @money($data['direct_costs_indirect_costs_unit']) </td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td style="vertical-align: top !important; padding-left: 20px">
                <table class="table table-light">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="3">
                                AIU
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <table>
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
                                            <td style="text-transform: uppercase" colspan="2">
                                                <b>SubTotal + Administrativos + Imprevistos</b>
                                            </td>
                                            <td class="text-right">@money($data['administrative_Unforeseen_subTotal'])
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-transform: uppercase" colspan="2">
                                                <b>SubTotal + Administrativos + Imprevistos Unitario</b>
                                            </td>
                                            <td class="text-right">
                                                @money($data['administrative_Unforeseen_unit'])
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-transform: uppercase" colspan="2">Utilidad</td>
                                            <td class="text-right"> {{ $data['utility_percentage'] }}% </td>
                                        </tr>
                                        <tr>
                                            <td style="text-transform: uppercase" colspan="2">SubTotal + Admin +
                                                Imprevisto +
                                                Utilidad</td>
                                            <td class="text-right"> @money($data['admin_unforeseen_utility_subTotal']) </td>
                                        </tr>
                                        <tr>
                                            <td style="text-transform: uppercase" colspan="2">SubTotal + Admin +
                                                Imprevisto +
                                                Utilidad Unitario</td>
                                            <td class="text-right"> @money($data['admin_unforeseen_utility_unit']) </td>
                                        </tr>
                                        <tr>
                                            <td style="text-transform: uppercase" colspan="2">Precio Venta Total
                                                COP + Retención
                                            </td>
                                            <td class="text-right"> @money($data['sale_price_cop_withholding_total']) </td>
                                        </tr>
                                        <tr>
                                            <td style="text-transform: uppercase" colspan="2">Valor de Venta
                                                Unitario COP</td>
                                            <td class="text-right"> @money($data['sale_value_cop_unit']) </td>
                                        </tr>
                                        <tr>
                                            <td style="text-transform: uppercase" colspan="2">TRM</td>
                                            <td class="text-right"> @money($data['trm']) </td>
                                        </tr>
                                        <tr>
                                            <td style="text-transform: uppercase" colspan="2">Precio Venta Total
                                                USD + Retención
                                            </td>
                                            <td class="text-right"> USD @money($data['sale_price_usd_withholding_total']) </td>
                                        </tr>
                                        <tr>
                                            <td style="text-transform: uppercase" colspan="2">Valor de Venta
                                                Unitario USD</td>
                                            <td class="text-right"> USD @money($data['sale_value_usd_unit']) </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </td>
        </tr>
    </tbody>
</table>
