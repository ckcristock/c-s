<?php

namespace App\Http\Controllers;

use App\Models\Geometry;
use App\Models\GeometryMeasure;
use App\Models\Measure;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class GeometryController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->success(
            Geometry::with('measures')
            ->when($request->name, function ($q, $fill) {
                $q->where('name', 'like', '%' . $fill . '%');
            })
            ->when($request->limit, function ($query) {
                return $query->limit(10);
            })
            ->get(['id', 'image', 'weight_formula', DB::raw('UPPER(name) AS text'), 'id As value']));
    }

    public function paginate()
    {
        return $this->success(
            Geometry::orderBy('name')
                ->when(request()->get('name'), function ($q, $fill) {
                    $q->where('name', 'like', '%' . $fill . '%');
                })
                ->paginate(request()->get('pageSize', 50), ['*'], 'page', request()->get('page', 1))
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
    public function store(Request $request)
    {
        try {
            $data = $request->except(["measures"]);
            if ($data["image"] != '') {
                $data["image"] = URL::to('/') . '/api/image?path=' . saveBase64($data["image"], 'geometries/', true);
            }
            $geometry = Geometry::create($data);
            $measures = request()->get('measures');

            foreach ($measures as $measure) {
                GeometryMeasure::create([
                    'geometry_id' => $geometry->id,
                    'measure_id'  => $measure
                ]);
            }
            return $this->success('creacion exitosa');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), $th->getLine(), $th->getFile(), 500);
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
        return $this->success(
            Geometry::with('measures')
                ->find($id, ['id', 'image', 'weight_formula', 'name As text', 'id As value'])
        );
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
        try {
            $geometry = Geometry::find($id);
            $measures = request()->get('measures');

            $geometry->update($request->all());

            GeometryMeasure::where('geometry_id', $geometry->id)->delete();

            foreach ($measures as $measure) {
                GeometryMeasure::create([
                    'geometry_id' => $geometry->id,
                    'measure_id'  => $measure
                ]);
            }
            return response()->json([
                "message" => "Se ha actualizado con éxito",
            ]);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
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
