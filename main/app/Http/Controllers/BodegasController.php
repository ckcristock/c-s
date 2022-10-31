<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Bodegas;
use App\Models\GrupoEstiba;
use App\Models\Estiba;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\URL;

class BodegasController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(Bodegas::with('grupo_estibas')->get());
    }

    public function bodegasConGrupos($id)
    {
        return $this->success(
            GrupoEstiba::where('Id_Bodega_Nuevo', $id)
            ->when(request()->get('Nombre'), function ($q, $fill) {
                $q->where('Nombre', 'like', '%' . $fill . '%');
            })
            ->when(request()->get('Fecha_Vencimiento'), function ($q, $fill) {
                $q->where('Fecha_Vencimiento', '=', $fill );
            })
            ->when(request()->get('Presentacion'), function ($q, $fill) {
                $q->where('Presentacion', '=', $fill );
            })->paginate(request()->get('pageSize', 5), ['*'], 'page', request()->get('page', 1)));
    }

    public function gruposConEstibas($id)
    {
        return $this->success(
            Estiba::where('Id_Grupo_Estiba', $id)
            ->when(request()->get('Nombre'), function ($q, $fill) {
                $q->where('Nombre', 'like', '%' . $fill . '%');
            })->when(request()->get('Estado'), function ($q, $fill) {
                $q->where('Estado', '=', $fill );
            })->when(request()->get('Codigo_Barras'), function ($q, $fill) {
                $q->where('Codigo_Barras', 'like', '%' . $fill . '%' );
            })->paginate(request()->get('pageSize', 5), ['*'], 'page', request()->get('page', 1)));
    }

    public function paginate()
    {
        return $this->success(
            Bodegas::when(request()->get('Nombre'), function ($q, $fill) {
                $q->where('Nombre', 'like', '%' . $fill . '%');
            })->when(request()->get('Direccion'), function ($q, $fill) {
                $q->where('Direccion', 'like', '%' . $fill . '%');
            })->when(request()->get('Telefono'), function ($q, $fill) {
                $q->where('Telefono', 'like', '%' . $fill . '%');
            })->when(request()->get('Compra_Internacional'), function ($q, $fill) {
                $q->where('Compra_Internacional', '=', $fill);
            })->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function activarInactivar(){
        return $this->success(
            Bodegas::where('Id_Bodega_Nuevo',request()->get('id'))
            ->update(['Estado' => request()->get('state')])
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        try {
            $camposPorRegistrar = [
                'Nombre'=> request()->get('nombre'),
                'Direccion'=> request()->get('direccion'),
                'Telefono'=> request()->get('telefono'),
                'Compra_Internacional'=> request()->get('compraInternacional')
            ];
            $type = '.'. request()->get('typeMapa');
            if(!is_null(request("typeMapa"))){
                if (in_array(request("typeMapa"),['jpeg','jpg','png'])) {
                    $base64 = saveBase64(request("mapa"), 'mapas_bodegas/', true, $type);
                    $url=URL::to('/') . '/api/image?path=' . $base64;
                } else {
                    throw new Exception(
                        "No se ha encontrado un formato de imagen válido (".request("typeMapa")."), revise e intente nuevamente"
                    );
                }
                $camposPorRegistrar['Mapa'] = $url;
            }
            $value = Bodegas::updateOrCreate( [ 'Id_Bodega_Nuevo'=> request()->get('id') ] , $camposPorRegistrar );
            return ($value->wasRecentlyCreated) ? $this->success('Creado con éxito') : $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return $this->errorResponse( [ "file" => $th->getFile(),"text" => $th->getMessage()]);
        }
    }

    public function storeGrupo()
    {
        try {
            $value = GrupoEstiba::updateOrCreate( [ 'Id_Grupo_Estiba'=> request()->get('id') ] , [
                'Nombre'=> request()->get('nombre'),
                'Presentacion'=> request()->get('presentacion'),
                'Fecha_Vencimiento'=> request()->get('fechaVencimiento'),
                'Id_Bodega_Nuevo'=> request()->get('idBodega')
            ] );
            return ($value->wasRecentlyCreated) ? $this->success('Creado con éxito') : $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return $this->errorResponse( $th->getFile().$th->getMessage() );
        }
    }

    public function storeEstiba()
    {
        try {
            $value = Estiba::updateOrCreate( [ 'Id_Estiba'=> request()->get('id') ] , [
                'Nombre'=> request()->get('nombre'),
                'Id_Grupo_Estiba'=> request()->get('idGrupo'),
                'Id_Bodega_Nuevo'=> request()->get('idBodega'),
                'Codigo_Barras'=> request()->get('codigoBarras'),
                'Estado'=> request()->get('estado')
            ] );
            return ($value->wasRecentlyCreated) ? $this->success('Creado con éxito') : $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return $this->errorResponse( $th->getFile().$th->getMessage() );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->success(Bodegas::where('Id_Bodega_Nuevo',$id)->first());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
