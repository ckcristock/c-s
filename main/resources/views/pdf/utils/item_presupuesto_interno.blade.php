    <div class="text-center m-4">
        <h5>Elementos del presupuesto</h5>
    </div>

    @foreach ($data->items as $item)
        <div class="text-center"> <strong>ITEM {{ $loop->index + 1 }} </strong></div>

        <table class="div-small table-border">
            <thead>
                <tr style="background:#E1EEC0; text-transform: uppercase">
                    <th colspan="6">Costos Directos</th>
                    <th> Cost. Indirectos</th>
                    <th> ADM + IMP + UTI </th>
                    <th colspan="4"> Valor Venta </th>
                </tr>
                <tr style="background:#E1EEC0; text-transform: uppercase">
                    <th>#</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>C. Unitario</th>
                    <th>VT. Costo</th>
                    <th>SubT. Indirectos</th>
                    <th>ADM+IMP+UTI</th>
                    <th>Otros Cargos</th>
                    <th>%</th>
                    <th>V/U Venta COP</th>
                    <th>V/U Venta USD</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($item->subitems as $subItem)
                    <tr class="text-uppercase">
                        <td class="text-center"> {{ $loop->parent->index + 1 }}.{{ $loop->index + 1 }}</td>
                        <td class="text-center">
                            {{ $subItem->type == 'S' ? 'Servicio' : 'Producto' }}
                        </td>
                        <td class="text-center">
                            @if ($subItem->apu_set_id)
                                <span> {{ $subItem->apuSet->name }}</span>
                            @endif

                            @if ($subItem->apu_part_id)
                                <span> {{ $subItem->apuPart->name }}</span>
                            @endif

                            @if ($subItem->apu_service_id)
                                <span>{{ $subItem->apuService->name }}</span>
                            @endif

                            @if (!$subItem->type_module)
                                <span>{{ $subItem->description }}</span>
                            @endif

                        </td>
                        <td class="text-center">{{ $subItem->cuantity }}</td>
                        <td class="text-right"> @money($subItem->unit_cost)</td>
                        <td class="text-right"> @money($subItem->total_cost) </td>
                        <td class="text-right"> @money($subItem->subtotal_indirect_cost)</td>
                        <td class="text-right"> @money($subItem->total_amd_imp_uti) </td>
                        <td class="text-right">@money($subItem->another_values)</td>
                        <td class="text-right"> {{ $subItem->percentage_sale }}%</td>
                        <td class="text-right"> @money($subItem->value_cop) </td>
                        <td class="text-right">@money($subItem->value_usd) </td>
                    </tr>
                @endforeach
                <tr style="background-color: #c6c6c6">
                    <td colspan="5">SUBTOTAL ITEM {{ $loop->index + 1 }}</td>
                    <td class="text-right">
                        @money($item->total_cost)
                    </td>
                    <td class="text-right">

                        @money($item->subtotal_indirect_cost)
                    </td>
                    <td class="text-right">
                        @money($item->total_amd_imp_uti)
                    </td>
                    <td class="text-right">
                        @money($item->another_values)
                    </td>
                    <td class="text-right">
                        {{ $item->percentage_sale }}%
                    </td>
                    <td class="text-right">

                        @money($item->value_cop)
                    </td>
                    <td class="text-right">

                        @money($item->value_usd)
                    </td>
                </tr>
            </tbody>
        </table>
    @endforeach
