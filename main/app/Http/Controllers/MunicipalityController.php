<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class MunicipalityController extends Controller
{
    use ApiResponser;

    public function allMunicipalities()
    {
        return $this->success(
            Municipality::all(['name as text', 'id as value'])
        );
    }
    public function municipalitiesForDep($idDep)
    {
        return $this->success(
            Municipality::where('department_id', $idDep)->orderBy('name', 'asc')->get(['name as text', 'id as value'])
        );
    }

    public function index()
    {
        $data = Municipality::with([
            'department' => function ($q) {
                $q->select('id', 'name');
            }
        ])
            ->orderBy('name', 'DESC')
            ->when(Request()->get('department_id'), function ($q, $param) {
                $q->where('department_id', $param);
            })
            ->get(['name As text', 'id As value', 'department_id', 'code']);
        return $this->success($data);
    }

    public function paginate()
    {
        return $this->success(
            Municipality::orderBy('name')
                ->with([
                    'department' => function ($q) {
                        $q->select('id', 'name')->when(Request()->get('department'), function ($q, $fill) {
                            $q->where('name', 'like', '%' . $fill . '%');
                        });
                    }
                ])
                ->whereHas('department', function ($q) {
                    $q->when(request()->get('department'), function ($q, $fill) {

                        $q->where('name', 'like', '%' . $fill . '%');
                    });
                })
                ->when(Request()->get('code'), function ($q, $fill) {
                    $q->where('code', 'like', '%' . $fill . '%');
                })
                ->when(Request()->get('name'), function ($q, $fill) {
                    $q->where('name', 'like', '%' . $fill . '%');
                })
                ->paginate(Request()->get('pageSize', 10), ['*'], 'page', Request()->get('page', 1))
        );
    }

    public function store(Request $request)
    {
        try {
            $validate_code = Municipality::where('code', $request->get('code'))->first();
            if ($validate_code) {
                return $this->error('El código del Municipio ya existe', 423);
            }
            Municipality::updateOrCreate(['id' => $request->get('id')], $request->all());
            return $this->success('creacion exitosa');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 200);
        }
    }
}
