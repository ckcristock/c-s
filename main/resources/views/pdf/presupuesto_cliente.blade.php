<style>
    * {
        font-family: 'Roboto', sans-serif;
    }

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
</style>
@include('components/cabecera', [$company, $datosCabecera, $image])
<div class="div">
    <div class="blocks-50">
        <strong>Cliente:</strong>
        {{ $data->customer->name }}
    </div>
    <div class="blocks">
        <strong>Destino:</strong>
        {{ $data->destiny->name }}
    </div>
    <div class="blocks">
        <strong>Proyecto:</strong>
        {{ $data->project }}
    </div>
</div>
<div class="div">
    <div class="blocks-50">
        <strong>Linea:</strong>
        {{ $data->line }}
    </div>
    <div class="blocks">
        <strong>TRM:</strong>
        @money($data->trm)
    </div>
</div>

<!-- Configuracion presupuestal -->

<!--  END  Configuracion presupuestal -->
<table class="div mt-1">
    <tbody>
        <tr>
            <td><strong>OBSERVACIONES</strong></td>
        </tr>
        <tr>
            <td>
                {{ isset($data->observation) ? $data->observation : 'No existen observaciones.' }}
        </tr>
    </tbody>
</table>
<!-- ITEMS -->
@include('pdf.utils.item_presupuesto')
<!-- ITEMS -->

<table class="table table-borderless">
    <thead>
        <tr>
            <th colspan="2" class="text-center">TOTAL PRESUPUESTO</th>
        </tr>
    </thead>
    <tbody>
        @if ($currency == 'cop')
            <tr>
                <td>TOTAL COP $ </td>
                <td class="text-right" style="width:90px"> @money($data->total_cop)</td>
            </tr>
        @endif
        @if ($currency == 'usd')
            <tr>
                <td>TOTAL USD $ </td>
                <td class="text-right" style="width:90px"> @money($data->total_usd)</td>
            </tr>
        @endif

    </tbody>
</table>
