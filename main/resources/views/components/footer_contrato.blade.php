<style>
    .table-borderless tbody+tbody,
    .table-borderless td,
    .table-borderless th,
    .table-borderless thead th {
        border: 0;
    }

    .text-left {
        text-align: left !important;
    }
</style>
<table class="table table-borderless" style="margin-top: 30px">
    <thead>
        <tr>
            <th><strong>EL EMPLEADOR</strong></th>
            <th><strong>EL EMPLEADO</strong></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="padding-top: 3rem">
                ____________________________________
            </td>
            <td style="padding-top: 3rem">
                ____________________________________
            </td>
        </tr>
        <tr>
            <td>ALBERTO LUIS BALCARCEL</td>
            <td>{{ strtoupper($person->person) }}
            </td>
        </tr>
        <tr>
            <td>C.C. 13.835.833</td>
            <td>C.C. {{ number_format($person->identifier, 0, '', '.') }}</td>
        </tr>
    </tbody>
</table>
