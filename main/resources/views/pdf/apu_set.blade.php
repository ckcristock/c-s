<style>
    *{
      font-family:'Roboto', sans-serif
    }
    .page-content {
        width: 750px;
    }

    .row {
        display: inline-block;
        width: 100%;
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

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center
    }
</style>

<div style="width: 100%;">
    <div class="blocks" style="width: 60%;">
        <img src="{{public_path('/assets/img/logo.png')}}" style="width:120px;" />
        <p>
            MaqMo<br>
            N.I.T.: 10001<br>
            Calle 12 #20-20 - TEL: 302323
        </p>

    </div>
    <div class="blocks" style="width: 40%; text-align: right;">
        <h4 style="margin: 0; padding: 0;" >APU Conjunto</h4>
        <h5 style="margin: 0; padding: 0;"> {{$data['created_at']}} </h5>

    </div>
</div>

<hr style="border:1px dotted #ccc; ">

<div style="width: 100%; font-size: 10px; ">
    <div class="blocks" style="width: 50%;">
        <strong>Nombre:</strong>
        {{$data['name']}}
    </div>
    <div class="blocks">
        <strong>Cliente:</strong>
        {{$data['thirdparty']['first_name']}} {{$data['thirdparty']['first_surname']}}
    </div>
    <div class="blocks">
        <strong>Destino:</strong>
        {{$data['city']['name']}}
    </div>
</div>

<div style="width: 100%; font-size: 10px; ">
    <div class="blocks" style="width: 50%;">
        <strong>Quien elabora:</strong>
        {{$data['person']['first_name']}} {{$data['person']['first_surname']}}
    </div>

    <div class="blocks">
        <strong>Linea:</strong>
        {{$data['line']}}
    </div>
</div>

<div style="width: 100%; font-size: 10px">
    <div class="blocks" style="width: 100%;font-size: 10px">
        <table>
            <thead>
                <tr>
                    <td>
                        <div class="title">
                            <h5>Observaciones</h5>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ $data['observation'] }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>



@if (count($data['setpartlist']) > 0)
<table style="font-size:10px;margin-top:10px;" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <td style="font-size: 20px;" colspan="6">
                <h6>
                    Listado de Piezas Conjunto
                </h6>
            </td>
        </tr>
        <tr style="background:#c6c6c6;">
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Unidad</th>
            <th>Cantidad</th>
            <th>Costo Unidario</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['setpartlist'] as $setpartlist )
        <tr>
            <td style="text-align: center;"> {{ $setpartlist['apu_type'] }} </td>
            @if ($setpartlist['apu_type'] == 'pieza')
                <td style="text-align: center;"> {{ $setpartlist['apupart']['name'] }} </td>
            @endif
            @if ($setpartlist['apu_type'] == 'conjunto')
                <td style="text-align: center;"> {{ $setpartlist['apuset']['name'] }} </td>
            @endif
            <td style="text-align: center;"> {{ $setpartlist['unit'] }} </td>
            <td style="text-align: center;"> {{ $setpartlist['amount'] }} </td>
            <td style="text-align: center;"> {{ $setpartlist['unit_cost'] }} </td>
            <td style="text-align: right;"> {{ $setpartlist['total'] }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="row">
    <p class="text-right" style="font-size: 10px;">
        <strong>Subtotal: </strong>
        {{ $data['list_pieces_sets_subtotal'] }}
    </p>
</div>
@endif

@if (count($data['machine']) > 0)
<table style="font-size:10px;margin-top:10px;" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <td style="font-size: 20px;" colspan="5">
                <h6>
                    Maquinas Herramientas
                </h6>
            </td>
        </tr>
        <tr style="background:#c6c6c6;">
            <th>Descripción</th>
            <th>Unidad</th>
            <th>Cantidad</th>
            <th>Costo Unitario</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['machine'] as $machine )
        <tr>
            <td style="text-align: center;"> {{ $machine['description'] }} </td>
            <td style="text-align: center;"> {{ $machine['unit'] }} </td>
            <td style="text-align: center;"> {{ $machine['amount'] }} </td>
            <td style="text-align: right;"> {{ $machine['unit_cost'] }} </td>
            <td style="text-align: right;"> {{ $machine['total'] }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="row">
    <p class="text-right" style="font-size: 10px;">
        <strong>Subtotal: </strong>
        {{ $data['machine_tools_subtotal'] }}
    </p>
</div>
@endif

@if (count($data['internal']) > 0)
<table style="font-size:10px;margin-top:10px;" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <td style="font-size: 20px;" colspan="5">
                <h6>
                    Procesos Internos
                </h6>
            </td>
        </tr>
        <tr style="background:#c6c6c6;">
            <th>Descripción</th>
            <th>Unidad</th>
            <th>Cantidad</th>
            <th>Costo Unitario</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['internal'] as $internal )
        <tr>
            <td style="text-align: center;"> {{ $internal['description'] }} </td>
            <td style="text-align: center;"> {{ $internal['unit'] }} </td>
            <td style="text-align: center;"> {{ $internal['amount'] }} </td>
            <td style="text-align: right;"> {{ $internal['unit_cost'] }} </td>
            <td style="text-align: right;"> {{ $internal['total'] }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="row">
    <p class="text-right" style="font-size: 10px;">
        <strong>Subtotal: </strong>
        {{ $data['internal_proccesses_subtotal'] }}
    </p>
</div>
@endif

@if (count($data['external']) > 0)
<table style="font-size:10px;margin-top:10px;" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <td style="font-size: 20px;" colspan="5">
                <h6>
                    Procesos Externos
                </h6>
            </td>
        </tr>
        <tr style="background:#c6c6c6;">
            <th>Descripción</th>
            <th>Unidad</th>
            <th>Cantidad</th>
            <th>Costo Unitario</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['external'] as $external )
        <tr>
            <td style="text-align: center;"> {{ $external['description'] }} </td>
            <td style="text-align: center;"> {{ $external['unit'] }} </td>
            <td style="text-align: center;"> {{ $external['amount'] }} </td>
            <td style="text-align: right;"> {{ $external['unit_cost'] }} </td>
            <td style="text-align: right;"> {{ $external['total'] }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="row">
    <p class="text-right" style="font-size: 10px;">
        <strong>Subtotal: </strong>
        {{ $data['external_proccesses_subtotal'] }}
    </p>
</div>
@endif

@if (count($data['other']) > 0)
<table style="font-size:10px;margin-top:10px;" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <td style="font-size: 20px;" colspan="5">
                <h6>
                    Otros
                </h6>
            </td>
        </tr>
        <tr style="background:#c6c6c6;">
            <th>Descripción</th>
            <th>Unidad</th>
            <th>Cantidad</th>
            <th>Costo Unitario</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['other'] as $other )
        <tr>
            <td style="text-align: center;"> {{ $other['description'] }} </td>
            <td style="text-align: center;"> {{ $other['unit'] }} </td>
            <td style="text-align: center;"> {{ $other['amount'] }} </td>
            <td style="text-align: right;"> {{ $other['unit_cost'] }} </td>
            <td style="text-align: right;"> {{ $other['total'] }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="row">
    <p class="text-right" style="font-size: 10px;">
        <strong>Subtotal: </strong>
        {{ $data['others_subtotal'] }}
    </p>
</div>
@endif

<hr style="border:1px dotted #ccc;">


<div class="blocks" style="width: 49%;margin-top: 30px">
    <table>
        <thead>
            <tr>
                <th colspan="3">
                    <h6 class="text-center">Costos Indirectos</h6>
                    <hr style="border:1px dotted #ccc; width: 100%">
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['indirect'] as $indirect)
            <tr>
                <td>{{$indirect['name']}}</td>
                <td class="text-center">
                    {{ $indirect['percentage'] }}
                </td>
                <td class="text-right">
                    {{ $indirect['value']}}
                </td>
            </tr>
            @endforeach
            <tr>
                <td class="text-center">Costo Indirectos</td>
                <td></td>
                <td class="text-right">{{$data['indirect_cost_total']}}</td>
            </tr>
        </tbody>
    </table>
    <table>
        <tbody>
            <tr>
                <td>Costos Directos + Costos Indirectos Totales</td>
                <td class="text-right"> {{$data['direct_costs_indirect_costs_total']}} </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="blocks" style="width: 50%;margin-top: 30px">
    <table>
        <thead>
            <tr>
                <th colspan="3">
                    <h6 class="text-center">AIU</h6>
                    <hr style="border:1px dotted #ccc; width: 100%">
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Administrativos</td>
                <td class="text-center">
                    {{ $data['administrative_percentage'] }}
                </td>
                <td class="text-right">
                    {{ $data['administrative_value'] }}
                </td>
            </tr>
            <tr>
                <td>Imprevistos</td>
                <td class="text-center">
                    {{ $data['unforeseen_percentage'] }}
                </td>
                <td class="text-right">
                    {{ $data['unforeseen_value'] }}
                </td>
            </tr>
            <tr>
                <td>SubTotal + Administrativos + Imprevistos</td>
                <td class="text-center">
                    {{ $data['administrative_unforeseen_subtotal'] }}
                </td>
                <td class="text-right">
                    {{ $data['administrative_unforeseen_unit'] }}
                </td>
            </tr>
        </tbody>
    </table>
    <table>
        <tbody>
            <tr>
                <td>Utilidad</td>
                <td class="text-right"> {{ $data['utility_percentage'] }} </td>
            </tr>
            <tr>
                <td>SubTotal + Admin + Imprevisto + Utilidad</td>
                <td class="text-right"> {{ $data['admin_unforeseen_utility_subtotal'] }} </td>
            </tr>
        </tbody>
    </table>
    <table>
        <tbody>
            <tr>
                <td>Precio Venta Total COP + Retención</td>
                <td class="text-right"> {{$data['sale_price_cop_withholding_total']}} </td>
            </tr>
            <tr>
                <td>TRM</td>
                <td class="text-right"> {{$data['trm']}} </td>
            </tr>
        </tbody>
    </table>
    <table>
        <tbody>
            <tr>
                <td>Precio Venta Total USD + Retención</td>
                <td class="text-right"> {{$data['sale_price_usd_withholding_total']}} </td>
            </tr>
        </tbody>
    </table>
</div>