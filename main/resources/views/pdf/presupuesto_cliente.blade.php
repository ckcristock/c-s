<style>
    * {
        font-family: 'Roboto', sans-serif;
        font-size: 14px;

    }

    /*   .card-header{
        text-align: center;
    } */

    .table-items thead>tr>th {
        /*   color: rgb(18, 95, 196); */
        border: 1px solid gainsboro;
        border-radius: 2px;
    }

    .table-items input {}

    .table-items th {
        text-align: center;
        font-weight: 900;
    }

    /* .values {
    padding: 5px 0px;
    margin: 0!important;

    text-align: right;
  } */
    .fa,
    .fas,
    .far {
        cursor: pointer;
    }



    .detachable {
        cursor: pointer;
    }

    .form-control-plaintext {
        background-color: transparent;
    }


    .header-item {
        background-color: rgb(255, 255, 255);
        color: #232247;
    }

    /*  */


    .page-content {
        width: 100%;
    }

    .row {
        display: inline-block;
        width: 100%;
    }



    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 5px;
    }

    .section {
        padding: 10px;
        width: 100%;
        background-color: gainsboro;
    }

    .td-header {
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

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center
    }

    .table tbody  tr {
        background-color: #F8F9FA;
    }

    .table tbody tr  td {
        padding: 5px 2px;
    }

    .table tbody  tr:nth-child(odd) {
        background-color: #EFF2F7;
        color: black;
    }

    .card {
        display: block;

        border-radius: 5px;
        padding: 10px 8px;
        margin-bottom: 5px;
    }

    .blocks {
        text-transform: uppercase;
        /*     width: 60%; */
        display: inline-block;
    }

    .block {
        text-transform: uppercase;

        /*     width: 60%; */
        /* display: flex; */
        /*  background-color: red; */
        width: 100%;
        position: relative
    }

    h3 {
        font-size: 20px !important;
    }

    .child {
        position: absolute;
        top: 0;
        height: 100px;
    }
</style>
@include('components/cabecera', [$company, $datosCabecera, $image])
<div class="page-content">


    <div class="card">
        <table class="table">
            <tr>
                <td> <strong> Cliente:</strong>
                    {{$data->customer->name}}
                </td>
                <td> <strong> Destino:</strong>
                    {{$data->destiny->name}}
                </td>
                <td> <strong> Proyecto:</strong> {{$data->project}}
                </td>
            </tr>
            <tr>
                <td> <strong> Linea:</strong>
                    {{$data->line}}
                </td>
                <td colspan="2"> <strong> TRM:</strong>
                    {{$data->trm}}
                </td>

            </tr>
        </table>

        <!-- Configuracion presupuestal -->

        <!--  END  Configuracion presupuestal -->

        <div class="row mb-2">
            <div class="col-12">
                <div class="form-group">
                    <p>
                        Observaciones:
                        {{$data->observation}}

                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- ITEMS -->
    @include('pdf.utils.item_presupuesto')
    <!-- ITEMS -->

    <div class="col-12 card card-body">
        <table class="table">

            <thead>
                <tr>
                    <th colspan="2" class="text-center">TOTAL PRESUPUESTO</th>
                </tr>
            </thead>
            <tbody>
                @if($currency=='cop')
                <tr>
                    <td>TOTAL COP $ </td>
                    <td class="text-right" style="width:90px"> {{$data->total_cop }}</td>
                </tr>
                @endif
                @if($currency=='usd')
                <tr>
                    <td>TOTAL USD $ </td>
                    <td class="text-right" style="width:90px"> {{$data->total_usd }}</td>
                </tr>
                @endif

            </tbody>
        </table>
    </div>


</div>
</div>
