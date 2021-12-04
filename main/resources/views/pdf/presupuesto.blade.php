<style>
    .table-items {
        font-size: 0.65rem;
    }

    .table-items thead>tr>th {
        color: rgb(18, 95, 196);
        border: 1px solid gainsboro;
        border-radius: 2px;
    }

    .table-items input {
        font-size: 0.65rem;
    }

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
    * {
        font-family: 'Roboto', sans-serif
    }

    .page-content {
        width: 100%;
    }

    .row {
        display: inline-block;
        width: 100%;
    }


    td,
    th {
        font-size: 13px;
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

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center
    }


    .card {
        display: block;
        background-color: gray;
        padding: 5px 3px;
        margin-bottom: 5px;
    }
</style>

<div class="page-content">

    <div class="mb-4">
        <div class="card">
            <h4 class="t">PRESUPUESTO DE PROYECTO MAQUINADOS Y MONTAJES</h4>
        </div>
    </div>

    <div class="card">
        <table>
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
                <td> <strong> TRM:</strong>
                    {{$data->trm}}
                </td>

            </tr>
        </table>

        <!-- Configuracion presupuestal -->
        <div style="margin-top:10px ;">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th class="text-center" colspan="3">
                            <h5>% De configuración presupuestal</h5>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-light">

                    @foreach ( $data->indirectCosts as $item)
                    <tr>
                        <td> {{$item->indirectCost->name}} </td>
                        <td class="text-right">
                            {{ $item->percentage}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!--  END  Configuracion presupuestal -->

        <div class="row mb-2">
            <div class="col-12">
                <div class="form-group">
                    <label for="staticEmail">Observaciones</label>
                    {{$data->observation}}
                </div>
            </div>
        </div>
    </div>
    <!-- ITEMS -->
    @include('pdf.utils.item_presupuesto')
    <!-- ITEMS -->

    <div class="col-12 card card-body">
        <table class="table table-sm table-striped">

            <thead>
                <tr>
                    <th colspan="2" class="text-center">TOTAL PRESUPUESTO</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>TOTAL COP $ </td>
                    <td class="text-right" style="width:90px"> {{$data->total_cop }}</td>
                </tr>
                <tr>
                    <td>TOTAL USD $ </td>
                    <td class="text-right" style="width:90px"> {{$data->total_usd }}</td>
                </tr>
                <tr>
                    <td>V/U VENTA PRORRATEADO COP $ </td>
                    <td class="text-right" style="width:90px"> {{$data->unit_value_prorrateado_cop }}</td>
                </tr>
                <tr>
                    <td>V/U VENTA PRORRATEADO USD $ </td>
                    <td class="text-right" style="width:90px"> {{$data->unit_value_prorrateado_usd }}</td>
                </tr>
            </tbody>
        </table>
    </div>


</div>
</div>