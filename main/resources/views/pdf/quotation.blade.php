@include('components/cabecera', [$company, $datosCabecera, $image])
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
<hr class="line" />
<h4 class="card-title">Señores:</h4>
<div class="card-text">
    {{ $data['client']['social_reason'] ? $data['client']['social_reason'] : $data['client']['full_name'] }}
    <br />
    {{ $data['municipality']['name'] }}
</div>
<div class="alert alert-primary" role="alert">
    <i class="fas fa-cogs"></i> {{ $data['line'] }} {{ $data['project'] }}
</div>
<div class="rounded-top table-responsive">
    <table class="table table-bordered table-striped table-sm">
        <thead class="bg-light">
            <tr class="text-center text-uppercase">
                <th>#</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Valor unitario</th>
                <th>Valor total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['items'] as $key => $item)
                <tr class="text-center">
                    <td>{{ $key + 1 }}</td>
                    <td class="text-left">
                        <b>{{ $item['name'] }}</b>
                        <p>Comprende:</p>
                        <ul class="mb-0">
                            @foreach ($item['subItems'] as $subitem)
                                <li>{{ $subitem['description'] }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>{{ $item['cuantity'] }}</td>
                    @if ($data['money_type'] == 'cop')
                        <td>@money($item['value_cop'])</td>
                        <td>@money($item['total_cop'])</td>
                    @endif
                    @if ($data['money_type'] == 'usd')
                        <td>@money($item['value_usd'])</td>
                        <td>@money($item['total_usd'])</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end">
    @if ($data['money_type'] == 'cop')
        <h5>TOTAL: @money($data['total_cop'])</h5>
    @endif
    @if ($data['money_type'] == 'usd')
        <h5>TOTAL: @money($data['total_usd'])</h5>
    @endif
</div>
<div style="font-size: 10px;">{!! $data->commercial_terms !!}</div>
