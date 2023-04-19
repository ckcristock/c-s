<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Services\consulta;
use App\Models\OrdenCompraNacional;
use Illuminate\Support\Facades\DB;

class OrdenCompraNacionalController extends Controller
{
    use ApiResponser;

    public function listarPendientes(Request $request)
    {
        $resultado = OrdenCompraNacional::with('person', 'third')
            ->whereNotIn('Estado', ['Recibida', 'Anulada'])
            ->where('Aprobacion', '=', 'Aprobada')
            ->where('Id_Bodega_Nuevo', '<>', 0)
            ->havingRaw('Id_Bodega_Nuevo')
            ->withCount(['products as Items'])
            ->when($request->codigo, function ($q, $fill) {
                $q->where('Codigo', 'like', "%$fill%");
            })
            ->when($request->proveedor, function ($q, $fill) {
                $q->whereHas('third', function ($query) use ($fill) {
                    $query->where(DB::raw('IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname))'), 'like', "%$fill%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('Codigo', 'desc')
            ->limit(20)
            ->get();
        //!Si se van a usar comoras internacionales aquí hay un ejemplo de como quedaría, no existen modelos aun
        /* $resultados = OrdenCompraInternacional::with(['funcionario', 'proveedor'])
            ->whereNotIn('Estado', ['Recibida', 'Anulada'])
            ->orderBy('Fecha_Registro', 'desc')
            ->orderBy('Codigo', 'desc')
            ->withCount(['productosOrdenCompraInternacional as Items'])
            ->get(); */

        return $this->success($resultado);
    }
}
