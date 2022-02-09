<?php

namespace App\Http\Controllers;

use App\Models\DisciplinaryProcess;
use App\Models\LegalDocument;
use App\Models\MemorandumInvolved;
use App\Models\PersonInvolved;
use App\Traits\ApiResponser;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class DisciplinaryProcessController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = DisciplinaryProcess::with([
            'person' => function($q){ 
                $q->select('id', 'first_name', 'second_name', 'first_surname', 'second_surname');
            }
        ])
        ->with('personInvolved')
        ->whereHas('personInvolved.person', function($q)
        {
            $q->when( request()->get('involved') , function($q, $fill)
            {
                $q->where(DB::raw('concat(first_name, " ",first_surname)'), 'like', '%' . $fill . '%');
            });
        })
        ->orWhereDoesntHave('personInvolved.person')
        ->whereHas('person', function($q) {
            $q->when( request()->get('person') , function($q, $fill)
                {
                    $q->where(DB::raw('concat(first_name, " ",first_surname)'), 'like', '%' . $fill . '%');
                });
        })
        ->when( request()->get('status'), function($q, $fill) {
            if (request()->get('status') == 'Todos') {
                return null;
            } else {
                $q->where('status', 'like', '%' . $fill . '%');
            }
            })
            ->when( request()->get('code'), function($q, $fill) {
                $q->where('code', 'like', '%' . $fill . '%');
            })
        /* $data = Request()->all();
        $page = key_exists('page', $data) ? $data['page'] : 1;
        $pageSize = key_exists('pageSize',$data) ? $data['pageSize'] : 5;
        return $this->success(
            DB::table('people as p')
                ->select(
                    'd.code',
                    'p.first_name',
                    'p.second_name',
                    'p.first_surname',
                    'p.second_surname',
                    'd.process_description',
                    'd.status',
                    'd.date_of_admission',
                    'd.date_end',
                    'd.id',
                    'd.file'
                )
                ->when( Request()->get('person') , function($q, $fill)
                {
                    $q->where(DB::raw('concat(p.first_name, " ",p.first_surname)'), 'like', '%' . $fill . '%');
                })
                ->join('disciplinary_processes as d', function($join) {
                    $join->on('d.person_id', '=', 'p.id');
                })
                ->when( Request()->get('status'), function($q, $fill) {
                    if (request()->get('status') == 'Todos') {
                        return null;
                    } else {
                        $q->where('d.status', 'like', '%' . $fill . '%');
                    }il
                })
                ->when( Request()->get('code'), function($q, $fill) {
                    $q->where('d.code', 'like', '%' . $fill . '%');
                }) */
                ->orderBy('created_at', 'DESC')
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1));
        return $this->success($query);
    }

    /* public function getHistory(){

    } */

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
            $data = $request->except(['involved']);
            $involves = request()->get('involved');
            $type = '.'. $request->type;
            $base64 = saveBase64File( $data["file"], 'evidencia/', false, $type);
            $data["file"] = URL::to('/') . '/api/file?path=' . $base64;
            $dp = DisciplinaryProcess::create([
                'person_id' => $request->person_id,
                'process_description' => $request->process_description,
                'date_of_admission' => $request->date_of_admission,
                'date_end' => $request->date_end,
                'file' => $base64
            ]);
            $id = $dp->id;
            $dp["code"] = 'P' . $id;
            $dp->save();
            foreach ($involves as $involved) {
                $base64 = saveBase64File($involved['file'], 'evidencia/', false, '.pdf');
                URL::to('/') . '/api/file?path=' . $base64;
                $annotation = PersonInvolved::create([
                    'user_id' => auth()->user()->id,
                    'observation' => $involved['observation'],
                    'file' => $base64,
                    'disciplinary_process_id' => $dp->id,
                    'person_id' => $involved['person_id']
                ]);
				/* $involved["disciplinary_process_id"] = $dp->id;
                $annotation = Annotation::create($involved); */
                foreach ($involved['memorandums'] as $memorandum) {
                   MemorandumInvolved::create([
                     'person_involved_id' => $annotation['id'],
                     'memorandum_id' => $memorandum['id']
                   ]);
                }
            }
            return $this->success('Creado con éxito');
        } catch (\Throwable $th) {
            return $this->error([$th->getMessage(), $th->getLine(), $th->getFile()], 500);
        }
    }

    public function descargoPdf($id)
    {
        $descargo = DB::table('disciplinary_processes as dp')
            ->select(
                'dp.person_id',
                'dp.id as descargo_id',
                'p.first_name',
                'p.second_name',
                'p.first_surname',
                'p.second_surname',
                'p.identifier',
                'dp.date_of_admission',
                'dp.created_at'
            )
            ->join('people as p','p.id', '=', 'dp.person_id')
            ->where('dp.id', $id)
            ->first();
        $company = DB::table('companies as c')
            ->select(
                'c.name as company_name',
                'c.document_number as nit',
                'c.phone',
                'c.email_contact',
                DB::raw('CURRENT_DATE() as fecha')
            )
            ->first();
        $pdf = PDF::loadView('pdf.descargopdf', [
            'descargo' => $descargo,
            'company' => $company
        ]);
        return $pdf->download('descargopdf.pdf');
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
            DB::table('memorandums as m')
                ->select(
                    'm.id',
                    'm.created_at as created_at_memorandum',
                    't.name as memorandumType',
                    'p.first_name',
                    'm.details'
                )
                ->join('people as p', function($join) {
                    $join->on('p.id', '=', 'm.person_id');
                })
                ->join('memorandum_types as t', function($join) {
                    $join->on('t.id', '=', 'm.memorandum_type_id');
                })
                ->where('p.id', '=', $id)
                ->get()
        );
    }

    public function process($id)
    {
        return $this->success(
            DB::table('disciplinary_processes as d')
                ->select(
                    'd.id',
                    'd.process_description',
                    'd.created_at as created_at_process'
                )
                ->join('people as p', function($join) {
                    $join->on('p.id', '=', 'd.person_id');
                })
                ->where('p.id', '=', $id)
                ->get()
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

    }

    public function legalDocument($id)
    {
        return $this->success(
            LegalDocument::where('disciplinary_process_id', $id)->first()
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
        // Conseguimos el objeto
        $proceso = DisciplinaryProcess::where('id', '=', $id)->first();
        try {
            // Si existe
            $proceso->fill($request->all());
            $proceso->save();
            // $base64 = saveBase64File($request->file, 'legal_documents/', false, '.pdf');
            // URL::to('/') . '/api/file?path=' . $base64;
            // LegalDocument::create([
            //     'file' => $base64,
            //     'disciplinary_process_id' => $id
            // ]);
            return $this->success($proceso);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error($proceso, 500);
        }
    }


    public function approve(Request $request, $id)
	{
		try {
			$approve = DisciplinaryProcess::find($id);
			$approve->update([
				'status' => $request->status,
				'approve_user_id' => auth()->user()->id
			]);
			return $this->success("Aprobado con éxito");
		} catch (\Throwable $th) {
			return $this->errorResponse($th->getMessage(), 500);
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
