@php
    use App\Http\Services\consulta;
    function getMovimientosCuenta($fecha1, $fecha2)
    {
        $query = "SELECT MC.Id_Plan_Cuenta, PC.Codigo, PC.Nombre, PC.Codigo_Niif, PC.Nombre_Niif, SUM(Debe) AS Debito, SUM(Haber) AS Credito FROM Movimiento_Contable MC INNER JOIN Plan_Cuentas PC ON MC.Id_Plan_Cuenta = PC.Id_Plan_Cuentas WHERE DATE(Fecha_Movimiento) BETWEEN '$fecha1' AND '$fecha2' AND MC.Estado != 'Anulado' GROUP BY MC.Id_Plan_Cuenta";

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $movimientos = $oCon->getData();
        unset($oCon);

        return $movimientos;
    }



    function obtenerSaldoAnterior($naturaleza, $array, $index, $tipo_reporte, $fecha_corte, $tipo_reporte_2)
    {
        $value = $tipo_reporte == 'Pcga' ? 'Codigo' : 'Codigo_Niif';

        $saldo_anterior = 0;
        $tipo_reporte = strtoupper($tipo_reporte);
        if ($naturaleza == 'D') {
            // Si es naturaleza debito, suma, de lo contrario, resta
            $saldo_anterior = $array[$index]->{'Debito_' . $tipo_reporte} - $array[$index]->{'Credito_' . $tipo_reporte};
        } else {
            $saldo_anterior = $array[$index]->{'Credito_' . $tipo_reporte} - $array[$index]->{'Debito_' . $tipo_reporte};
        }

        $fecha1 = date('Y-m-d', strtotime($fecha_corte));

        # VALIDACIÓN POR SI LA FECHA DE INICIO NO ES EL DÍA UNO (1) DEL MES Y SE TOQUE SACAR EL SALDO DE LA DIFERENCIA DEL ULTIMO BALANCE INICIAL.

        if ($fecha1 != '2019-01-01') {
            $fecha1 = '2019-01-01';
            $fecha2 = $fecha_corte;
            $movimientos_lista = getMovimientosCuenta($fecha1, $fecha2);
            $codigo = $array[$index]->$value;
            $tipo = $array[$index]->Tipo_P;
            $debito = calcularDebito($codigo, $tipo, $movimientos_lista, $tipo_reporte_2);
            $credito = calcularCredito($codigo, $tipo, $movimientos_lista, $tipo_reporte_2);
            $saldo_anterior = calcularNuevoSaldo($naturaleza, $saldo_anterior, $debito, $credito);
        }

        return $saldo_anterior;
    }

    function calcularNuevoSaldo($naturaleza, $saldo_anterior, $debito, $credito)
    {
        $nuevo_saldo = 0;

        if ($naturaleza == 'D') { // Si es naturaleza debito, suma, de lo contrario, resta
            $nuevo_saldo = ($saldo_anterior + $debito) - $credito;
        } else {
            $nuevo_saldo = ($saldo_anterior + $credito) - $debito;
        }

        return $nuevo_saldo;
    }

    function calcularCredito($codigo, $tipo_cuenta, $movimientos, $tipo_reporte)
    {
        // return '0'; // Esto es temporal.

        $codigos_temp = [];

        foreach ($movimientos as $mov) {
            $nivel = strlen($mov['Codigo']);
            $nivel2 = strlen($codigo);
            $cod_superior = '';
            $restar_str = 0;
            $cod_mov = $tipo_reporte == 'Pcga' ? $mov['Codigo'] : $mov['Codigo_Niif'];

            /* echo "++". $mov['Codigo'] ."<br>";
        echo "--". $codigo ."<br>";

        var_dump($this->compararCuenta($codigo, $nivel2, $cod_mov));
        echo "<br>"; */

            if (compararCuenta($codigo, $nivel2, $cod_mov)) {
                $codigos_temp[$cod_mov] = $mov['Credito'];
                while ($nivel > $nivel2) {
                    if ($nivel > 2) {
                        $restar_str += 2;

                        // echo "cod superior A.N -- " . $cod_superior . "<br>";
                        // echo "Nivel -- " . $nivel . " -- Resta -- " . $restar_str . "<br>";
                        $str = $cod_mov;
                        $count_str = strlen($str);
                        $cod_superior = substr($str, 0, $count_str - $restar_str);
                        // echo "cod superior -- " . $cod_superior . "<br>";


                        if (!array_key_exists($cod_superior, $codigos_temp)) {
                            $codigos_temp[$cod_superior] = $mov['Credito'];
                        } else {
                            $codigos_temp[$cod_superior] += $mov['Credito'];
                        }
                        $nivel -= 2;
                    } else {
                        $restar_str += 1;
                        // echo "cod superior A.N -- " . $cod_superior . "<br>";
                        // echo "Nivel -- " . $nivel . " -- Resta -- " . $restar_str . "<br>";
                        $str = $cod_mov;
                        $count_str = strlen($str);
                        $cod_superior = substr($str, 0, $count_str - $restar_str);
                        // echo "cod superior -- " . $cod_superior . "<br><br>";
                        if (!array_key_exists($cod_superior, $codigos_temp)) {
                            $codigos_temp[$cod_superior] = $mov['Credito'];
                        } else {
                            $codigos_temp[$cod_superior] += $mov['Credito'];
                        }
                        $nivel -= 1;
                    }
                }
            }
        }

        /* echo "<pre>";
    var_dump($codigos_temp);
    echo "</pre>"; */
        // exit;

        return isset($codigos_temp[$codigo]) ? $codigos_temp[$codigo] : '0';
    }

    function compararCuenta($codigo, $nivel, $cod_cuenta_actual)
    {
        $str_comparar = substr($cod_cuenta_actual, 0, $nivel);

        if (strpos($str_comparar, $codigo) !== false) {
            return true;
        }

        return false;
    }

    function calcularDebito($codigo, $tipo_cuenta, $movimientos, $tipo_reporte)
    {
        $codigos_temp = [];

        foreach ($movimientos as $mov) {
            $nivel = strlen($mov['Codigo']);
            $nivel2 = strlen($codigo);
            $cod_superior = '';
            $restar_str = 0;
            $cod_mov = $tipo_reporte == 'Pcga' ? $mov['Codigo'] : $mov['Codigo_Niif'];

            if (compararCuenta($codigo, $nivel2, $cod_mov)) {
                $codigos_temp[$cod_mov] = $mov['Debito'];
                while ($nivel > $nivel2) {
                    if ($nivel > 2) {
                        $restar_str += 2;

                        $str = $cod_mov;
                        $count_str = strlen($str);
                        $cod_superior = substr($str, 0, $count_str - $restar_str);

                        if (!array_key_exists($cod_superior, $codigos_temp)) {
                            $codigos_temp[$cod_superior] = $mov['Debito'];
                        } else {
                            $codigos_temp[$cod_superior] += $mov['Debito'];
                        }
                        $nivel -= 2;
                    } else {
                        $restar_str += 1;

                        $str = $cod_mov;
                        $count_str = strlen($str);
                        $cod_superior = substr($str, 0, $count_str - $restar_str);
                        if (!array_key_exists($cod_superior, $codigos_temp)) {
                            $codigos_temp[$cod_superior] = $mov['Debito'];
                        } else {
                            $codigos_temp[$cod_superior] += $mov['Debito'];
                        }
                        $nivel -= 1;
                    }
                }
            }
        }

        return isset($codigos_temp[$codigo]) ? $codigos_temp[$codigo] : '0';
    }

    function getNombreCuentaClase($codigo)
    {
        $codigo = substr($codigo, 0, 1);

        $cuentas_clase = ['ACTIVO', 'PASIVO', 'PATRIMONIO'];

        return $cuentas_clase[$codigo - 1];
    }
@endphp
<table class="table">
    <thead>
        <tr>
            <th colspan="3" rowspan="2">FECHA CORTE {{ $fecha }} | BALANCE DE GENERAL | CENTRO COSTO: NO
                APLICA</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($balance as $i => $value)
            @php
                $codigo = $tipo_reporte == 'Pcga' ? $value->Codigo : $value->Codigo_Niif;
                $nombre_cuenta = $tipo_reporte == 'Pcga' ? $value->Nombre : $value->Nombre_Niif;
                $saldo_anterior = obtenerSaldoAnterior($value->Naturaleza, $balance, $i, $tipo_reporte, $fecha_corte, $tipo_reporte_2);
                $debito = 0;
                $credito = 0;
                $nuevo_saldo = calcularNuevoSaldo($value->Naturaleza, $saldo_anterior, $debito, $credito);
            @endphp
            @if ($nivel_reporte > 1)
                @if (substr($codigo, 0, 1) != $cod_clase_temp)
                    <tr>
                        <td></td>
                        <td>{{ getNombreCuentaClase($codigo) }}</td>
                    </tr>
                    @php
                        $cod_clase_temp = substr($codigo, 0, 1);
                    @endphp
                @endif
            @else
                @php
                    $acum_saldos = 0;
                @endphp
            @endif
            @if ($nuevo_saldo != 0)
                <tr>
                    <td>{{ $codigo }}</td>
                    <td>{{ $value['Nombre'] }}</td>
                    <td>{{ $nuevo_saldo }}</td>
                </tr>
                @php
                    $acum_saldos += $nuevo_saldo;
                    if ($codigo === '1') {
                        $total_activo = $nuevo_saldo;
                    }
                    if ($codigo === '2') {
                        $total_pasivo = $nuevo_saldo;
                    }
                    if ($codigo === '3') {
                        $total_patrimonio = $nuevo_saldo;
                    }
                @endphp
            @endif
            @if ($i == count($balance) - 1)
                <tr>
                    <td></td>
                    <td>TOTAL PATRIMONIO</td>
                    <td>{{$total_patrimonio}}</td>
                </tr>
            @endif
        @endforeach
        <tr>
            <td></td>
            <td>RESULTADOS EJERCICIO</td>
            <td>{{$resultado_ejercicio}}</td>
        </tr>
        <tr>
            <td></td>
            <td>TOTAL PATRIMONIO CON LA UTILIDAD DEL EJERCICIO</td>
            <td>{{$total_patrimonio_utilidad_ejercicio}}</td>
        </tr>
        <tr>
            <td></td>
            <td>TOTAL PASIVO Y PATRIMONIO</td>
            <td>{{$total_pasivo_y_patrimonio}}</td>
        </tr>
    </tbody>
</table>
