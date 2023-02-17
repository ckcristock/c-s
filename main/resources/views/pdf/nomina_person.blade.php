@include('components.cabecera_dos')
    <div>
        <div>
            <table class="w-100">
                <tbody>
                    <tr>
                        <th>Funcionario</th>
                        <td>{{ $info['name'] }} {{ $info['surname'] }}</td>
                        <th>Documento</th>
                        <td>{{ number_format($info['identifier'], 0, ',', '.') }}</td>
                        {{-- <th>{{ $data['frecuencia_pago'] }}</th> {{-- datos de frecuencia --}}
                        <th>Frecuencia pago</th> {{-- datos de frecuencia --}}
                    </tr>
                    <tr>
                        <th>Cargo</th>
                        <td>{{ $info['position'] }}</td>
                        <th>Días Laborados</th>
                        <td>{{ $info['worked_days'] }}</td>
                        <td>{{ $data['frecuencia_pago'] }}</td>
                    </tr>
                    <tr>
                        <th>Salario</th>
                        <td> {{ number_format($info['basic_salary'], 2, ',', '.') }} </td>
                        <th>Fecha de Ingreso</th>
                        <td colspan="2">
                            {{ Carbon\Carbon::parse($info['date_of_admission'])->locale('es_ES')->isoFormat('dddd, D \d\e MMMM YYYY') }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
        </div>
        <div class=" ">
            <table class="w-100">
                <thead class="">
                    <tr>
                        <th colspan="2">Resumen del Pago</th>
                    </tr>
                    <tr>
                        <th>Item</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Salario</td>
                        <td class="text-right">{{ number_format($info['salario_neto'], 2, ',', '.') }}</td>
                    </tr>
                    @if ($info['transportation_assitance'] == 0)
                        <tr>
                            <td>Subsidio de Transporte</td>
                            <td class="text-right">{{ number_format($info['transportation_assitance'], 2, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>Total neto apagar al empleado</th>
                        <td class="text-right">{{ number_format($info['transportation_assitance'], 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Salario</td>
                        <td class="text-right">{{ number_format($info['transportation_assitance'], 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Subsidio de Transporte</td>
                        <td class="text-right">{{ number_format($info['transportation_assitance'], 2, ',', '.') }}</td>
                    </tr>
                    @if (false)
                        // hay_vacaciones
                        <tr>
                            <td>Vacaciones</td>
                            <td class="text-right">{{ number_format($info['transportation_assitance'], 2, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td>Retenciones y Deducciones</td>
                        <td class="text-right">
                            {{ number_format($info['deducciones']['valor_total'] + $info['retencion']['valor_total'], 2, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <th>Total neto a pagar al empleado</th>
                        <td class="text-right">{{ number_format($info['transportation_assitance'], 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <small class="text-center">
            <b>
                <br>
                Nota: Lo expuesto en este comprobante representa el pago {{ $data['frecuencia_pago'] }} del empleado, y en
                este
                se
                listan el salario neto, deducciones e ingresos adicionales y su firma representa su entera satisfacción.
                <br>
                <br>
            </b>
        </small>

        <div>
            <table class="w-100">
                <tbody class="text-center">
                    <tr>
                        <td>________________________________</td>
                        <td>________________________________</td>
                    </tr>
                    <tr>
                        <td class="uppercase"><b>
                                {{ $info['name'] }} {{ $info['surname'] }} <br>
                                {{ $info['position'] }}</b>
                        </td>
                        <td class="uppercase"><b>{{ $company->name }}</b></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
    </div>
</section>

