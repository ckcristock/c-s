<?php

namespace App\Http\Controllers;

use App\Models\ApuPart;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Models\ApuSet;
use App\Models\ApuSetExternalProcess;
use App\Models\ApuSetFile;
use App\Models\ApuSetIndirectCost;
use App\Models\ApuSetInternalProcess;
use App\Models\ApuSetMachineTool;
use App\Models\ApuSetOther;
use App\Models\ApuSetPartList;
use App\Models\Municipality;
use App\Services\ApuSetService;
use Barryvdh\DomPDF\Facade as PDF;

use Illuminate\Support\Facades\URL;


class ApuSetController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(
            ApuSetService::paginate()
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

        $data = $request->except([
            "files",
            "list_pieces_sets",
            "machine_tools",
            "internal_processes",
            "external_processes",
            "others",
            "indirect_cost",
        ]);

        $files = request()->get("files");
        $list_pieces_sets = request()->get("list_pieces_sets");
        $machine_tools = request()->get("machine_tools");
        $internal_processes = request()->get("internal_processes");
        $external_processes = request()->get("external_processes");
        $others = request()->get("others");
        $indirect_cost = request()->get("indirect_cost");

        try {
            $consecutive = getConsecutive('apu_sets');
            if ($consecutive->city) {
                $abbreviation = Municipality::where('id', $data['city_id'])->first()->abbreviation;
                $data['code'] = generateConsecutive('apu_sets', $abbreviation);
            } else {
                $data['code'] = generateConsecutive('apu_sets');
            }
            $apuset = ApuSet::create($data);
            $id = $apuset->id;
            $apuset->save();

            foreach ($files as $file){
                $base64 = saveBase64File($file, 'apu-sets/', false, '.pdf');
                $file = URL::to('/') . '/api/file?path=' . $base64;
                ApuSetFile::create(['apu_set_id' => $id, 'file' => $file]);
			}


            foreach ($list_pieces_sets as $lps){
				$lps["apu_set_id"] = $id;
				ApuSetPartList::create($lps);
			}
            foreach ($machine_tools as $mtool){
				$mtool["apu_set_id"] = $id;
				ApuSetMachineTool::create($mtool);
			}
            foreach ($internal_processes as $ip){
				$ip["apu_set_id"] = $id;
				ApuSetInternalProcess::create($ip);
			}
            foreach ($external_processes as $et){
				$et["apu_set_id"] = $id;
				ApuSetExternalProcess::create($et);
			}
            foreach ($others as $ot){
				$ot["apu_set_id"] = $id;
				ApuSetOther::create($ot);
			}
            foreach ($indirect_cost as $ic){
				$ic["apu_set_id"] = $id;
				ApuSetIndirectCost::create($ic);
			}
            sumConsecutive('apu_sets');
            return $this->success('Creado con éxito');


        } catch (\Throwable $th) {

                return $this->error($th->getMessage(), 500);

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
			ApuSetService::show($id)
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function find(Request $req)
    {
        //
        return $this->success(
			ApuSetService::find($req->get('name'))
		);
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
        $data = $request->except([
            "file",
            "list_pieces_sets",
            "machine_tools",
            "internal_processes",
            "external_processes",
            "others",
            "indirect_cost",
        ]);

        $files = request()->get("file");
        $list_pieces_sets = request()->get("list_pieces_sets");
        $machine_tools = request()->get("machine_tools");
        $internal_processes = request()->get("internal_processes");
        $external_processes = request()->get("external_processes");
        $others = request()->get("others");
        $indirect_cost = request()->get("indirect_cost");

        try {

            ApuSet::find($id)->update($data);

            if($list_pieces_sets){

                ApuSetPartList::where("apu_set_id", $id)->delete();

                foreach ($list_pieces_sets as $lps){
                    $lps["apu_set_id"] = $id;
                    ApuSetPartList::create($lps);
                }
            }

            if($machine_tools){

                ApuSetMachineTool::where("apu_set_id", $id)->delete();

                foreach ($machine_tools as $mt){
                    $mt["apu_set_id"] = $id;
                    ApuSetMachineTool::create($mt);
                }
            }
            if($internal_processes){

                ApuSetInternalProcess::where("apu_set_id", $id)->delete();

                foreach ($internal_processes as $ip){
                    $ip["apu_set_id"] = $id;
                    ApuSetInternalProcess::create($ip);
                }
            }
            if($external_processes){

                ApuSetExternalProcess::where("apu_set_id", $id)->delete();

                foreach ($external_processes as $ep){
                    $ep["apu_set_id"] = $id;
                    ApuSetExternalProcess::create($ep);
                }
            }
            if($others){

                ApuSetOther::where("apu_set_id", $id)->delete();

                foreach ($others as $ot){
                    $ot["apu_set_id"] = $id;
                    ApuSetOther::create($ot);
                }
            }
            if($indirect_cost){

                ApuSetIndirectCost::where("apu_set_id", $id)->delete();

                foreach ($indirect_cost as $ic){
                    $ic["apu_set_id"] = $id;
                    ApuSetIndirectCost::create($ic);
                }
            }

            return $this->success('Creado con éxito');

        }catch (\Throwable $th) {

            return $this->error($th->getMessage(), 500);

        }

    }

    public function apuParts()
    {
        return $this->success(
            ApuPart::select('name', 'unit_direct_cost', 'id')
            ->selectRaw('name as text, id as value')
            ->when( request()->get('name'), function($q, $fill)
            {
                $q->where('name','like','%'.$fill.'%');

            })
            ->limit(100)
            ->get()
        );
    }

    public function apuSets()
    {
        return $this->success(
            ApuSet::select('name', 'id', 'total_direct_cost')
            ->when( request()->get('name'), function($q, $fill)
            {
                $q->where('name','like','%'.$fill.'%');

            })
            ->limit(100)
            ->get()
        );
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


    public function activateOrInactivate(Request $request)
    {
        try {
            $state = ApuSet::find($request->get('id'));
            $state->update($request->all());
            return $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function pdf($id)
    {
        $data = ApuSetService::show($id);
		$pdf = PDF::loadView('pdf.apu_set', ['data' => $data]);
		return $pdf->download('apu_set.pdf');
    }
}
