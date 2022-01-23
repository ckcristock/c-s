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

            <table width="100%" cellspacing="0" class="table  table-items">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        @if($currency=='cop')
                        <th>V/U Venta COP</th>
                        <th>V/T Venta COP</th>
                        @endif
                        @if($currency=='usd')
                        <th>V/U Venta USD</th>
                        <th>V/T Venta USD</th>
                        @endif

                    </tr>
                </thead>
                <tbody>
                    @foreach($item->subitems as $subItem)
                    <tr>
                        <td> {{$loop->parent->index + 1 }}.{{$loop->index+1}}</td>
                        <td>

                            @if($subItem->apu_set_id)
                            <span> {{$subItem->apuSet->name}}</span>
                            @endif

                            @if($subItem->apu_part_id)
                            <span> {{$subItem->apuPart->name}}</span>
                            @endif

                            @if($subItem->apu_service)
                            <span>22{{$subItem->apuService->name}}</span>
                            @endif

                            @if(!$subItem->type_module)
                            <span>{{$subItem->description}}</span>
                            @endif

                        </td>

                        <td class="text-right">
                            {{$subItem->cuantity}}
                        </td>
                        @if($currency=='cop')
                        <td class="text-right">
                            {{ $subItem->value_cop  /  $subItem->cuantity }}
                        </td>
                        <td class="text-right" style="width: 110px;">
                            {{ $subItem->value_cop }}
                        </td>
                        @endif
                        @if($currency=='usd')
                        <td class="text-right">
                            {{ $subItem->value_usd  /  $subItem->cuantity }}
                        </td>

                        <td class="text-right" style="width: 110px;">
                            {{ $subItem->value_usd }}
                        </td>
                        @endif
                    </tr>
                    @endforeach
                    <tr class="bg-gray">
                        <td colspan="4">SUBTOTAL ITEM {{$loop->index + 1}}</td>
                        @if($currency=='cop')
                        <td class="text-right">
                            {{ $item->value_cop}}
                        </td>
                        @endif
                        @if($currency=='usd')
                        <td class="text-right">
                            {{ $item->value_usd}}

                        </td>
                        @endif

                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>