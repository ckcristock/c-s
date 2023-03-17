<style>
    .page-content {
        width: 750px;
    }

    .row {
        display: inline-block;
        width: 100%;
    }

    td {
        background-color: transparent;
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

    .text-center {
        text-align: center
    }

    .div {
        width: 100%;
        font-size: 10px;
    }

    th {
        text-align: left !important;
        font-size: 0.7rem;
    }

    td {
        font-size: 0.7rem;
    }
</style>
@include('components/cabecera', [$company, $datosCabecera, $image])
<hr style="border:1px dotted #ccc; ">
<table>
    <tbody>
        <tr>
            <th style="width: 16%;">Nombre</th>
            <td style="width: 16%;">{{ $data['name'] }}</td>
            <th style="width: 16%;">Cliente</th>
            <td style="width: 16%;">{{ $data['thirdparty']['name'] }}</td>
            <th style="width: 16%;">Destino</th>
            <td style="width: 16%;">{{ $data['city']['name'] }}</td>
        </tr>
        <tr>
            <th>Quién elabora</th>
            <td>{{ $data['person']['name'] }}</td>
            <th>Línea</th>
            <td>{{ $data['line'] }}</td>
        </tr>
        <tr>
            <th colspan="6">Observaciones</th>
        </tr>
        <tr>
            <td colspan="6"> {{ $data['observation'] }}</td>
        </tr>
    </tbody>
</table>

@if (count($data['dimensionalValidation']) > 0)
    <hr />
    <h5 class="mt-2 text-center">Validación dimensional</h5>
    <hr />
    <h6>Cálculo de mano de obra</h6>

    <div class="rounded-top table-responsive">
        <table class="table table-bordered table-striped table-sm">
            <thead>
                <tr class="table-primary">
                    <th colspan="4"></th>
                    <th colspan="5" class="text-center">Desplazamiento</th>
                    <th colspan="5" class="text-center">Ordinarias</th>
                    <th colspan="6" class="text-center">Festivas</th>
                    <th></th>
                </tr>
            </thead>
            <thead class="bg-light">
                <tr class="text-center text-uppercase">
                    <th class="align-middle">Item</th>
                    <th class="align-middle">Perfil</th>
                    <th class="align-middle">Tipo Desplaza</th>
                    <th class="align-middle">N. Persona</th>
                    <th class="align-middle">N. Días</th>
                    <th class="align-middle">Jornada</th>
                    <th class="align-middle">Horas</th>
                    <th class="align-middle">Valor Horas</th>
                    <th class="align-middle">Valor Total</th>
                    <th class="align-middle">N. Días</th>
                    <th class="align-middle">Jornada</th>
                    <th class="align-middle">Horas</th>
                    <th class="align-middle">Valor Hora</th>
                    <th class="align-middle">Valor Total</th>
                    <th class="align-middle">N. Días</th>
                    <th class="align-middle">Jornada</th>
                    <th class="align-middle">Horas</th>
                    <th class="align-middle">Valor Hora</th>
                    <th class="align-middle">Valor Total</th>
                    <th class="align-middle">Valor Salarial</th>
                </tr>
            </thead>
            @foreach ($data['dimensionalValidation'] as $key => $dimentional)
                <tbody>
                    <tr class="text-center">
                        <td class="align-middle">
                            {{ $key + 1 }}
                        </td>
                        <td class="align-middle">
                            {{ $dimentional['profiles']['profile'] }}
                        </td>
                        <td class="align-middle">
                            {{ $dimentional['displacement_type'] }}
                        </td>
                        <td class="align-middle">
                            {{ $dimentional['people_number'] }}
                        </td>
                        <td class="align-middle">
                            {{ $dimentional['days_number_displacement'] }}
                        </td>
                        <td class="align-middle">
                            {{ $dimentional['workind_day_displacement'] }}
                        </td>
                        <td class="align-middle">
                            {{ $dimentional['hours_displacement'] }}
                        </td>
                        <td class="align-middle">
                            @money($dimentional['hours_value_displacement'])
                        </td>
                        <td class="align-middle">
                            @money($dimentional['total_value_displacement'])
                        </td>
                        <td class="align-middle">
                            {{ $dimentional['days_number_ordinary'] }}
                        </td>
                        <td class="align-middle">
                            {{ $dimentional['working_day_ordinary'] }}
                        </td>
                        <td class="align-middle">
                            {{ $dimentional['hours_ordinary'] }}
                        </td>
                        <td class="align-middle">
                            @money($dimentional['hours_value_ordinary'])
                        </td>
                        <td class="align-middle">
                            @money($dimentional['total_value_ordinary'])
                        </td>
                        <td class="align-middle">
                            {{ $dimentional['days_number_festive'] }}
                        </td>
                        <td class="align-middle">
                            {{ $dimentional['working_day_festive'] }}
                        </td>
                        <td class="align-middle">
                            {{ $dimentional['hours_festive'] }}
                        </td>
                        <td class="align-middle">
                            @money($dimentional['hours_value_festive'])
                        </td>
                        <td class="align-middle">
                            @money($dimentional['total_value_festive'])
                        </td>
                        <td class="align-middle">
                            @money($dimentional['salary_value'])
                        </td>
                    </tr>
                    <tr>
                        <td colspan="100">
                            <div class="rounded-top table-responsive">
                                <table class="table table-bordered table-striped table-sm w-100">
                                    <thead>
                                        <tr class="table-primary">
                                            <th class="text-center">Perfil</th>
                                            <th colspan="4" class="text-center">Viáticos</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <thead class="bg-light">
                                        <tr class="text-center text-uppercase">
                                            <th colspan="2">Descripción</th>
                                            <th>Unidad</th>
                                            <th>Cantidad</th>
                                            <th>Valor Unitario</th>
                                            <th>Valor Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dimentional['travelEstimationDimensionalValidations'] as $key2 => $travelE)
                                            <tr class="text-center">
                                                <td class="align-middle" colspan="2">
                                                    {{ $travelE['description'] }}
                                                </td>
                                                <td class="align-middle">
                                                    {{ $travelE['unit'] }}
                                                </td>
                                                <td class="align-middle">
                                                    {{ $travelE['amount'] }}
                                                </td>
                                                <td class="align-middle">
                                                    @money($travelE['unit_value'])
                                                </td>
                                                <td class="align-middle">
                                                    @money($travelE['total_value'])
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" class="text-right text-uppercase">
                                                Subtotal
                                            </td>
                                            <td colspan="6" class="text-center">
                                                @money($travelE['subtotal'])
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            @endforeach
        </table>
    </div>
    <div class="row d-flex justify-content-end">
        <div class="col-md-6">
            <div class="d-flex justify-content-between">
                <div class="rounded-top table-responsive">
                    <table class="table table-hover">
                        <tbody>
                            <tr>
                                <td>SUBTOTAL MANO DE OBRA</td>
                                <td class="text-right">
                                    @money($data['subtotal_labor'])
                                </td>
                            </tr>
                            <tr>
                                <td>SUBTOTAL VIÁTICOS</td>
                                <td class="text-right">
                                    @money($data['subtotal_travel_expense'])
                                </td>
                            </tr>
                            <tr>
                                <td>SUBTOTAL VALID DIMENSIONAL</td>
                                <td class="text-right">
                                    @money($data['subtotal_dimensional_validation'])
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif



@if (count($data['assembliesStartUp']) > 0)
    <hr />
    <h5 class="text-center">Montaje y puesta en marcha</h5>
    <hr />
    <h6>Cálculo de mano de obra</h6>
    <div class="rounded-top table-responsive">
        <table class="table table-bordered table-striped table-sm">
            <thead>
                <tr class="table-primary">
                    <th colspan="4"></th>
                    <th colspan="5" class="text-center">Desplazamiento</th>
                    <th colspan="5" class="text-center">Ordinarias</th>
                    <th colspan="6" class="text-center">Festivas</th>
                    <th></th>
                </tr>
            </thead>
            <thead class="bg-light">
                <tr class="text-center text-uppercase">
                    <th class="align-middle">Item</th>
                    <th class="align-middle">Perfil</th>
                    <th class="align-middle">Tipo Desplaza</th>
                    <th class="align-middle">N. Persona</th>
                    <th class="align-middle">N. Días</th>
                    <th class="align-middle">Jornada</th>
                    <th class="align-middle">Horas</th>
                    <th class="align-middle">Valor Horas</th>
                    <th class="align-middle">Valor Total</th>
                    <th class="align-middle">N. Días</th>
                    <th class="align-middle">Jornada</th>
                    <th class="align-middle">Horas</th>
                    <th class="align-middle">Valor Hora</th>
                    <th class="align-middle">Valor Total</th>
                    <th class="align-middle">N. Días</th>
                    <th class="align-middle">Jornada</th>
                    <th class="align-middle">Horas</th>
                    <th class="align-middle">Valor Hora</th>
                    <th class="align-middle">Valor Total</th>
                    <th class="align-middle">Valor Salarial</th>
                </tr>
            </thead>
            @foreach ($data['assembliesStartUp'] as $key => $assemblies)
                <tbody>
                    <tr class="text-center">
                        <td class="align-middle">
                            {{ $key + 1 }}
                        </td>
                        <td class="align-middle" *ngIf="item.profiles">
                            {{ $assemblies['profiles']['profile'] }}
                        </td>
                        <td class="align-middle">
                            {{ $assemblies['displacement_type'] }}
                        </td>
                        <td class="align-middle">
                            {{ $assemblies['people_number'] }}
                        </td>
                        <td class="align-middle">
                            {{ $assemblies['days_number_displacement'] }}
                        </td>
                        <td class="align-middle">
                            {{ $assemblies['workind_day_displacement'] }}
                        </td>
                        <td class="align-middle">
                            {{ $assemblies['hours_displacement'] }}
                        </td>
                        <td class="align-middle">
                            @money($assemblies['hours_value_displacement'])
                        </td>
                        <td class="align-middle">
                            @money($assemblies['total_value_displacement'])
                        </td>
                        <td class="align-middle">
                            {{ $assemblies['days_number_ordinary'] }}
                        </td>
                        <td class="align-middle">
                            {{ $assemblies['working_day_ordinary'] }}
                        </td>
                        <td class="align-middle">
                            {{ $assemblies['hours_ordinary'] }}
                        </td>
                        <td class="align-middle">
                            @money($assemblies['hours_value_ordinary'])
                        </td>
                        <td class="align-middle">
                            @money($assemblies['total_value_ordinary']){
                        </td>
                        <td class="align-middle">
                            {{ $assemblies['days_number_festive'] }}
                        </td>
                        <td class="align-middle">
                            {{ $assemblies['working_day_festive'] }}
                        </td>
                        <td class="align-middle">
                            {{ $assemblies['hours_festive'] }}
                        </td>
                        <td class="align-middle">
                            @money($assemblies['hours_value_festive'])
                        </td>
                        <td class="align-middle">
                            @money($assemblies['total_value_festive'])
                        </td>
                        <td class="align-middle">
                            @money($assemblies['salary_value'])
                        </td>
                    </tr>
                    <tr>
                        <td colspan="100">
                            <div class="rounded-top table-responsive">
                                <table class="w-100 table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr class="table-primary">
                                            <th class="text-center">Perfil</th>
                                            <th colspan="4" class="text-center">Viáticos</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <thead class="bg-light">
                                        <tr class="text-center text-uppercase">
                                            <th colspan="2">Descripción</th>
                                            <th>Unidad</th>
                                            <th>Cantidad</th>
                                            <th>Valor Unitario</th>
                                            <th>Valor Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($assemblies['travelEstimationAssembliesStartUp'] as $key2 => $travelE)
                                            <tr class="text-center">
                                                <td colspan="2">
                                                    {{ $travelE['description'] }}
                                                </td>
                                                <td>
                                                    {{ $travelE['unit'] }}
                                                </td>
                                                <td>
                                                    {{ $travelE['amount'] }}
                                                </td>
                                                <td>@money($travelE['unit_value'])</td>
                                                <td>@money($travelE['total_value'])</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" class="text-right text-uppercase">
                                                Subtotal
                                            </td>
                                            <td colspan="6" class="text-center">
                                                @money($assemblies['subtotal'])
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            @endforeach
        </table>
    </div>
    <div class="row d-flex justify-content-end">
        <div class="col-md-6">
            <div class="d-flex justify-content-between">
                <div class="rounded-top table-responsive">
                    <table class="table table-hover">
                        <tbody>
                            <tr>
                                <td>SUBTOTAL MANO DE OBRA</td>
                                <td class="text-right">
                                    @money($data['subtotal_labor_mpm'])
                                </td>
                            </tr>
                            <tr>
                                <td>SUBTOTAL VIÁTICOS</td>
                                <td class="text-right">
                                    @money($data['subtotal_travel_expense_mpm'])
                                </td>
                            </tr>
                            <tr>
                                <td>SUBTOTAL MONTAJE Y PUESTA EN MARCHA</td>
                                <td class="text-right">
                                    @money($data['subtotal_assembly_commissioning'])
                                </td>
                            </tr>
                            <tr>
                                <td>SUBTOTAL GENERAL VIÁTICOS + MANO DE OBRA</td>
                                <td class="text-right">
                                    @money($data['general_subtotal_travel_expense_labor'])
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
<hr />
<h4 class="text-center">AIU</h4>
<hr />
<div class="d-flex justify-content-center">
    <div class="rounded-top table-responsive">
        <table class="table table-bordered table-striped table-sm text-center">
            <tbody>
                <tr>
                    <td class="text-left">Administrativos</td>
                    <td>{{ $data['administrative_percentage'] }}%</td>
                    <td>@money($data['administrative_value'])</td>
                </tr>
                <tr>
                    <td class="text-left">Imprevistos</td>
                    <td>{{ $data['unforeseen_percentage'] }}%</td>
                    <td>@money($data['unforeseen_value'])</td>
                </tr>
                <tr>
                    <td class="text-left" colspan="2">Subtotal A + I</td>
                    <td>
                        @money($data['subtotal_administrative_unforeseen'])
                    </td>
                </tr>
                <tr>
                    <td class="text-left" colspan="2">Utilidad</td>
                    <td>{{ $data['utility_percentage'] }}%</td>
                </tr>
                <tr>
                    <td class="text-left" colspan="2">SubTotal A + I + U</td>
                    <td>
                        @money($data['subtotal_administrative_unforeseen_utility'])
                    </td>
                </tr>
                <tr>
                    <td class="text-left" colspan="2">
                        Precio venta COP incluye retención
                    </td>
                    <td>
                        @money($data['sale_price_cop_withholding_total'])
                    </td>
                </tr>
                <tr>
                    <td class="text-left" colspan="2">TRM</td>
                    <td>@money($data['trm'])</td>
                </tr>
                <tr>
                    <td class="text-left" colspan="2">
                        Precio venta USD incluye retención
                    </td>
                    <td>@money($data['sale_price_usd_withholding_total'])</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
