@include('components/cabecera', [$company, $datosCabecera, $image])
<style>
    * {
        font-family: 'Roboto', sans-serif;
    }

    table {
        width: 100%;
        border-collapse: collapse;
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

    .text-center {
        text-align: center
    }

    .div {
        width: 100%;
        font-size: 10px;
    }

    .alert {
        position: relative;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        border-radius: 0.25rem;
        font-size: 12px
    }

    .alert-primary {
        color: #4d6410;
        background-color: #eaf3d2;
        border-color: #e1eec0;
    }

    .text-left {
        text-align: left;
    }
</style>
<h5 class="mb-0">Señores:</h5>
<div style="font-size: 12px">
    {{ $data['third_person']['name'] }} -
    {{ $data['client']['social_reason'] ? $data['client']['social_reason'] : $data['client']['full_name'] }}
    <br />
    <small style="font-size: 10px">{{ $data['municipality']['name'] }}</small>
</div>
<div class="alert alert-primary mt-1" role="alert">
    <i class="fas fa-cogs"></i> {{ strtoupper($data['description']) }}
</div>
<table class="div table-border">
    <thead >
        <tr style="background:#E1EEC0;">
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
                    @if (count($item['subItems']) > 0)
                    <p>Comprende:</p>
                    <ul class="mb-0">
                        @foreach ($item['subItems'] as $subitem)
                            <li>{{ $subitem['description'] }}</li>
                        @endforeach
                    </ul>
                    @endif
                </td>
                <td>{{ $item['cuantity'] }}</td>
                @if ($data['money_type'] == 'cop')
                    <td>@money($item['value_cop']) (SIN IVA)</td>
                    <td>@money($item['total_cop']) (SIN IVA)</td>
                @endif
                @if ($data['money_type'] == 'usd')
                    <td>@money($item['value_usd'])</td>
                    <td>@money($item['total_usd'])</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
<div class="d-flex justify-content-end">
    @if ($data['money_type'] == 'cop')
        <h5>TOTAL SIN IVA: @money($data['total_cop'])</h5>
    @endif
    @if ($data['money_type'] == 'usd')
        <h5>TOTAL: @money($data['total_usd'])</h5>
    @endif
</div>
<div style="font-size: 10px;">{!! $data->commercial_terms !!}</div>
<div style="font-size: 10px;">{!! $data->legal_requirements !!}</div>
<div style="font-size: 10px;">{!! $data->technical_requirements !!}</div>
