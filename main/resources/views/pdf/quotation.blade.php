@include('components/cabecera', [$company, $datosCabecera, $image])
<hr class="line" />
<h5 class="card-title">Señores:</h5>
<p class="card-text">
    {{ $data['client']['social_reason'] ? $data['client']['social_reason'] : $data['client']['full_name'] }}
    <br />
    {{ $data['municipality']['name'] }}
</p>
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
                        <td>${{ $item['value_cop'] }}</td>
                        <td>${{ $item['total_cop'] }}</td>
                    @endif
                    @if ($data['money_type'] == 'usd')
                        <td>{{ $item['value_usd'] }}</td>
                        <td>{{ $item['total_usd'] }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end">
    <ng-container *ngIf="quotation.money_type == 'cop'; else usd_total">
        <h5>TOTAL: ${{ $data['total_cop'] }}</h5>
    </ng-container>
    <ng-template #usd_total>
        <h5>TOTAL: {{ $data['total_usd'] }}</h5>
    </ng-template>
</div>
{{-- <div class="alert alert-light alert-custom-quotation pb-0" [innerHTML]="quotation.commercial_terms" role="alert"
    [ngClass]="'alert-p-0'"></div> --}}
