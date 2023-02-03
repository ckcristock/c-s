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

    .text-center {
        text-align: center
    }
</style>
@include('components/cabecera', [$company, $datosCabecera, $image])
<table class="table table-borderless">
    <tbody>
        <tr>
            <td> <strong> Cliente:</strong>
                {{ $data->customer->name }}
            </td>
            <td> <strong> Destino:</strong>
                {{ $data->destiny->name }}
            </td>
            <td> <strong> Proyecto:</strong> {{ $data->project }}
            </td>
        </tr>
        <tr>
            <td> <strong> Linea:</strong>
                {{ $data->line }}
            </td>
            <td colspan="2"> <strong> TRM:</strong>
                @money($data->trm)
            </td>
        </tr>
    </tbody>
</table>

<!-- Configuracion presupuestal -->

<!--  END  Configuracion presupuestal -->

<div class="row mb-2">
    <p>
        Observaciones:
        {{ $data->observation }}
    </p>
</div>
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
