@include('components.cabecera_contrato')
<div>

    <div class="text-center">
        COLILLA DE PAGO DE PRIMA DE SERVICIOS</div>
    <span class="badge badge-pill badge-success">Periodo  $bonus->lapse  </span>
    <div class="float-right">Comprobante Nro.</div>
    <br>
    <div>DATOS DEL TRABAJADOR</div>
    <br>
    <div class="font-weight-bold">Nombre: <span class="font-weight-normal">  $bonus->fullname </span></div>

    <div class="font-weight-bold">Documento: <span class="font-weight-normal">  $bonus->identifier </span></div>
    <div class="font-weight-bold">Día de pago:
        <span class="font-weight-normal">
             Carbon\Carbon::parse($bonus->payment_date)->locale('es_ES')->isoFormat('dddd, D \d\e MMMM YYYY') </span>
    </div>

    <br>
</div>
<div class="rounded-top table-responsive">
    <table class="table table-bordered table-sm text-nowrap">
        <thead class="bg-light">
            <tr class="text-center text-uppercase">
                <th>Fecha inicio</th>
                <th>Dias trabajados</th>
                <th>Salario Promedio</th>
                <th>Prima</th>
            </tr>
        </thead>
        <tbody>
            <tr class="text-center">
                <td>
                     $bonus->fecha_inicio </td>
                <td>
                     $bonus->worked_days </td>
                <td class="text-right">
                    $ number_format($bonus->average_amount, 2, ',', '.') </td>
                <td class="text-right">
                    $ number_format($bonus->amount, 2, ',', '.') </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="text-center">
     Carbon\Carbon::parse(Carbon\Carbon::today())->locale('es_ES')->isoFormat('dddd, D \d\e MMMM YYYY')
</div>
<br>
