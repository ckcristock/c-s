<div class="row ">
    <div class="col-12 text-center m-4">
        <h4>Elementos del presupuesto</h4>
    </div>

    @foreach($data->items as $item)
    <div class="card " style="width:100% ;">
        <div class="card-header header-item w-100">
            <div class=" d-flex align-items-center justify-content-between">
                <span class="bg-info p-2 text-white">{{ $loop->index + 1 }}</span>
                <span class="text-center"> <strong>ITEM {{ $loop->index  + 1 }} </strong></span>
            </div>
        </div>
        <div style="overflow-x:auto;" class="items table-responsive" class="card-body ">

            <table width="100%" cellspacing="0" class="table table-striped table-sm table-items">
                <thead>

                    <tr>
                        <th colspan="6">Costos Directos</th>
                        <th>

                            Cost. Indirectos
                        </th>
                        <th>

                            ADM + IMP + UTI
                        </th>
                        <th colspan="4">

                            Valor Venta
                        </th>

                        <th></th>

                        <th></th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>Tipo</th>
                        <th>Descripción</th>

                        <th>Cantidad</th>
                        <th>C. Unitario</th>
                        <th title="Valor Total Costo">VT. Costo</th>

                        <th title="Subtotal Indirectos">SubT. Indirectos</th>

                        <th>ADM+IMP+UTI</th>
                        <th>Otros Cargos</th>

                        <th>%</th>
                        <th>V/U Venta COP</th>
                        <th>V/U Venta USD</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($item->subitems as $subItem)
                    <tr>
                        <td> {{$loop->parent->index + 1 }}.{{$loop->index+1}}</td>
                        <td>
                            {{$subItem->type}}
                        </td>
                        <td>
                            <div style="position: relative;">
                                @if($subItem->apu_set)
                                <span>{{$subItem->$apu_set->name}}</span>
                                @endif

                                @if($subItem->apu_part)
                                <span>{{$subItem->$apu_part->name}}</span>
                                @endif

                                @if($subItem->apu_service)
                                <span>{{$subItem->$apu_service->name}}</span>
                                @endif

                                @if(!$subItem->type_module)
                                <span>{{$subItem->description}}</span>
                                @endif

                            </div>
                        </td>

                        <td class="text-right">
                            {{$subItem->cuantity}}
                        </td>
                        <td class="text-right">
                            {{$subItem->unit_cost}}

                        </td>
                        <td class="text-right">
                            {{$subItem->total_cost}}
                        </td>


                        <td class="text-right" style="width:90px">
                            {{$subItem->subtotal_indirect_cost }}
                        </td>

                        <td class="text-right" style="width:90px">
                            {{ $subItem->total_amd_imp_uti }}
                        </td>


                        <td class="text-right">
                            {{$subItem->another_values}}
                        </td>

                        <td class="text-center" style="width: 50px;">
                            % {{ $subItem->percentage_sale }}
                        </td>
                        <td class="text-right" style="width: 110px;">
                            {{ $subItem->value_cop }}
                        </td>
                        <td class="text-right" style="width: 110px;">
                            {{ $subItem->value_usd }}

                        </td>

                       
                    </tr>
                    @endforeach
                    <tr class="bg-gray">
                        <td colspan="5">SUBTOTAL ITEM {{$loop->index + 1}}</td>
                        <td class="text-right">
                            {{ $item->total_cost}}
                        </td>
                        <td class="text-right">

                            {{ $item->subtotal_indirect_cost}}
                        </td>
                        <td class="text-right">
                            {{ $item->total_amd_imp_uti}}
                        </td>
                        <td class="text-right">
                            {{ $item->another_values}}
                        </td>
                        <td class="text-center">
                            % {{ $item->percentage_sale }}
                        </td>
                        <td class="text-right">

                            {{ $item->value_cop}}
                        </td>
                        <td class="text-right">

                            {{ $item->value_usd}}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>