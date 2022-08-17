<?php

namespace App\Http\Controllers;

use App\Http\Requests\EpsRequest;
use App\Models\Eps;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class EpsController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Request()->all();
        $page = key_exists('page', $data) ? $data['page'] : 1;
        $pageSize = key_exists('pageSize', $data) ? $data['pageSize'] : 5;
        return $this->success(
            Eps::when(Request()->get('name'), function ($q, $fill) {
                $q->where('name', 'like', '%' . $fill . '%');
            })
                ->when(Request()->get('code'), function ($q, $fill) {
                    $q->where('code', 'like', '%' . $fill . '%');
                })
                ->paginate($pageSize, ['*'], 'page', $page)

        );
    }

    public function store(Request $request)
    {
        try {
            $eps = Eps::updateOrCreate(['id' => $request->get('id')], $request->all());
            return ($eps->wasRecentlyCreated) ? $this->success('Creado con éxito') : $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return  $this->errorResponse([$th->getMessage(), $th->getFile(), $th->getLine()]);
        }
    }
}
