@include('components/cabecera', [$company, $datosCabecera, $image])

<style>
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

    .align-bottom {
        vertical-align: bottom !important;
    }
</style>
<h5 class="mb-0">Señores:</h5>
<div style="font-size: 12px">
    {{ optional($data['client'])['social_reason'] ? strtoupper(optional($data['client'])['social_reason']) : strtoupper(optional($data['client'])['full_name']) }}
    <br />
    {{ strtoupper(optional($data['third_person'])['name']) }}
    <br>
    <small style="font-size: 10px">{{ strtoupper(optional($data['municipality'])['name']) }}</small>
</div>
<div class="alert alert-primary mt-1" role="alert">
    {{ strtoupper($data['description']) }}
</div>
@if (count($data['items']) > 0)
<table class="div table-border">
    <thead>
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
                    {!! $item['name'] !!}

                </td>
                <td>{{ $item['cuantity'] }}</td>
                @if ($data['money_type'] == 'cop')
                    <td class="text-right">@money($item['value_cop'])</td>
                    <td class="text-right">@money($item['total_cop'])</td>
                @endif
                @if ($data['money_type'] == 'usd')
                    <td class="text-right">USD @money($item['value_usd'])</td>
                    <td class="text-right">USD @money($item['total_usd'])</td>
                @endif
            </tr>
            @if (count($item['subItems']) > 0)
                @foreach ($item['subItems'] as $key2 => $subitem)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}.{{ $key2 + 1 }}</td>
                        <td>
                            <div>{!! $subitem['description'] !!}</div>
                        </td>
                        <td class="text-center">{{ $subitem['cuantity'] }}</td>
                        @if ($data['money_type'] == 'cop')
                            <td class="text-right">
                                @money($subitem['value_cop'])
                            </td>
                            <td class="text-right">
                                @money($subitem['total_cop'])
                            </td>
                        @endif
                        @if ($data['money_type'] == 'usd')
                            <td class="text-right">
                                USD @money($subitem['value_usd'])
                            </td>
                            <td class="text-right">
                                USD @money($subitem['total_usd'])
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endif
        @endforeach
    </tbody>
</table>
@endif
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
<table class="div">
    <tbody style="page-break-inside: avoid;">
        <tr>
            <th class="align-bottom" style="width: 50%"> <img src="{{ optional($creator)->signature }}"
                    style="max-width: 190px" /> </th>
            <th class="align-bottom" style="width: 50%"><img src="{{ optional($approve)->signature }}" style="max-width: 190px" /></th>
        </tr>
        <tr>
            <th>{{ strtoupper(optional($creator)->full_names) }}</th>
            <th>{{ strtoupper(optional($approve)->full_names) }}</th>
        </tr>
        <tr>
            <td class="text-center">CREACIÓN</td>
            <td class="text-center">APROBACIÓN</td>
        </tr>
    <tbody>
</table>
