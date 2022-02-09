<?php

namespace App\Http\Controllers;

use App\Models\MemorandumInvolved;
use App\Models\PersonInvolved;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class PersonInvolvedController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
            $base64 = saveBase64File($request->file, 'evidencia/', false, '.pdf');
                URL::to('/') . '/api/file?path=' . $base64;
                $annotation = PersonInvolved::create([
                    'user_id' => auth()->user()->id,
                    'observation' => $request->observation,
                    'file' => $base64,
                    'disciplinary_process_id' => $request->disciplinary_process_id,
                    'person_id' => $request->person_id
                ]);
                foreach ($request->memorandums as $memorandum) {
                   MemorandumInvolved::create([
                     'person_involved_id' => $annotation['id'],
                     'memorandum_id' => $memorandum['id']
                   ]);
                }
            return $this->success('Guardado con éxito');
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
            PersonInvolved::where('disciplinary_process_id', $id)
            ->where('state', 'Activo')
            ->with([
                'user' => function($q){
                    $q->select('id', 'person_id');
                }
            ])
            ->with([
                'person' => function($q){
                    $q->select('id', 'first_name', 'second_name', 'first_surname', 'second_surname');
                }
            ])
            ->with('memorandumInvolved')
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
            $annotation = PersonInvolved::where('id', '=', $id)->first();
            $annotation->fill($request->all());
            $annotation->save();
            return $this->success('Actualizado con éxito');
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
