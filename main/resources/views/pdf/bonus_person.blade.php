<div>
    <div
        style="text-align: center;
               font-weight: bolder;
               font-size: x-large;"
        >COLILLA DE PAGO DE PRIMA DE SERVICIOS</div>
        <div
        style="text-align: center;
               font-weight: bolder;"
        >Periodo {{$bonus->lapse}} </div>
    <br>
    <div style="text-align: right;">Comprobante Nro.</div>
    <br>
    <div style="text-align: center;
                font-weight: bolder;
                font-size: large;">
        DATOS DEL TRABAJADOR
    </div>
    <br>
    <div>
        <div style="width: 150px;
                    font-weight: bold;"
            >Nombre: </div>
        <div style="width: 350px;"> {{$bonus->fullname}}</div>
    </div>
    <div >
        <div style="width: 150px;
                    font-weight: bold;"
            >Documento: </div>
        <div style="width: 350px;"> {{$bonus->identifier}}</div>
    </div>
    <div >
        <div style="width: 150px;
                    font-weight: bold;"
            >Día de pago: </div>
        <div style="width: 350px;"> {{Carbon\Carbon::parse($bonus->payment_date)->locale('es_ES')->isoFormat('dddd, D \\d\\e MMMM YYYY')}}</div>
    </div>
    <br>
</div>

<div style="text-align: center;">
    <table style="justify-content: center;border-collapse: collapse;">
        <thead >
            <tr>
                <th style="width: 25px;">
                <th style="width: 150px;border-collapse: collapse; border: black 1px solid;">Fecha inicio</th>
                <th style="width: 150px;border-collapse: collapse; border: black 1px solid;">Dias trabajados</th>
                <th style="width: 150px;border-collapse: collapse; border: black 1px solid;">Salario Promedio</th>
                <th style="width: 150px;border-collapse: collapse; border: black 1px solid;">Prima</th>
            </tr>
        </thead>
        <tbody >
            <tr>
                <th style="width: 25px;">
                <td style="width: 150px;border-collapse: collapse; border: black 1px solid;text-align: right;">{{$bonus->fecha_inicio}}</td>
                <td style="width: 150px;border-collapse: collapse; border: black 1px solid;text-align: right;">{{$bonus->worked_days}}</td>
                <td style="width: 150px;border-collapse: collapse; border: black 1px solid;text-align: right;">{{number_format($bonus->average_amount,2,',','.')}}</td>
                <td style="width: 150px;border-collapse: collapse; border: black 1px solid;text-align: right;">{{number_format($bonus->amount,2,',','.')}}</td>
            </tr>
        </tbody>
    </table>
</div>
<br>
<br>
<div style="text-align: center;">
    {{ Carbon\Carbon::parse(Carbon\Carbon::today())->locale('es_ES')->isoFormat('dddd, D \\d\\e MMMM YYYY') }}
</div>
<br>


