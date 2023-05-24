<style>

    table {
        border-collapse: collapse;
    }

    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
    }

    .div {
        width: 100%;
        font-size: 9px;
    }

    .div-small td {
        font-size: 7px;
    }

    .div-small {
        width: 100%;
        font-size: 7px;
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

    table.table-border,
    .table-border th,
    .table-border td {
        border: 1px solid;
    }

    .table-bordered thead td,
    .table-bordered thead th {
        border-bottom-width: 2px;
    }

    .table-borderless tbody+tbody,
    .table-borderless td,
    .table-borderless th,
    .table-borderless thead th {
        border: 0;
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

    .text-left {
        text-align: left
    }

    .text-uppercase {
        text-transform: uppercase
    }
    .avoid {
        page-break-inside: avoid;
    }
</style>
@include('components/cabecera', [$company, $datosCabecera, $image])
<div class="page-content">
    <div class="div">
        <div class="blocks-50">
            <strong>Cliente:</strong>
            {{ optional($data->customer)->name }}
        </div>
        <div class="blocks">
            <strong>Destino:</strong>
            {{ optional($data->destiny)->name }}
        </div>

    </div>
    <div class="div">
        <div class="blocks-50">
            <strong>Proyecto:</strong>
            {{ $data->project }}
        </div>
        <div class="blocks">
            <strong>Linea:</strong>
            {{ $data->line }}
        </div>

    </div>
    <div class="div">
        <div class="blocks-50">
            <strong>TRM:</strong>
            @money($data->trm)
        </div>
    </div>

    <!-- Configuracion presupuestal -->
    <table class="div mt-4 avoid">
        <thead>
            <tr>
                <th class="text-center" colspan="2">
                    <h4>% DE CONFIGURACIÓN PRESUPUESTAL</h4>
                </th>
            </tr>
        </thead>
        <tbody class="bg-light">

            @foreach ($data->indirectCosts as $item)
                <tr>
                    <td> {{ optional($item->indirectCost)->name }} </td>
                    <td class="text-right">
                        {{ $item->percentage }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <!--  END  Configuracion presupuestal -->
    <div class="div mt-1 avoid">
        <div class="blocks-50">
            <strong>Observaciones:</strong>
        </div>
        <div class="blocks-50">
            {{ $data['observation'] ?? 'No existen observaciones.' }}
        </div>
    </div>
    <!-- ITEMS -->
    @include('pdf.utils.item_presupuesto_interno')
    <!-- ITEMS -->

    <div class="div mt-4 avoid">
        <table class="table table-sm table-striped">
            <tbody>
                <tr>
                    <td>TOTAL COP</td>
                    <td style="font-size: 13px;" class="text-right"> @money($data->total_cop)</td>
                </tr>
                <tr>
                    <td>TOTAL USD</td>
                    <td style="font-size: 13px;" class="text-right"> @money($data->total_usd)</td>
                </tr>
                <tr>
                    <td>V/U VENTA PRORRATEADO COP</td>
                    <td style="font-size: 13px;" class="text-right"> @money($data->unit_value_prorrateado_cop)</td>
                </tr>
                <tr>
                    <td>V/U VENTA PRORRATEADO USD</td>
                    <td style="font-size: 13px;" class="text-right"> @money($data->unit_value_prorrateado_usd)</td>
                </tr>
            </tbody>
        </table>
    </div>


</div>
</div>
