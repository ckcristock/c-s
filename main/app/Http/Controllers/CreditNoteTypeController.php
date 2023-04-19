<?php

namespace App\Http\Controllers;

use App\Models\CreditNoteType;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class CreditNoteTypeController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(CreditNoteType::where('status', 'Activo')->get(['id as value', 'name as text']));
    }

    public function paginate(Request $request) {
        return $this->success(
            CreditNoteType::orderBy('status')
                ->when($request->name, function ($q, $fill) {
                    $q->where('name', 'like', "%$fill%");
                })
                ->paginate(request()->get('pageSize', 15), ['*'], 'page', request()->get('page', 1))
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
            $creditNoteType = CreditNoteType::updateOrCreate(['id' => $request->id], $request->all());
            return $this->success($creditNoteType->wasRecentlyCreated ? 'Creado con éxito.' : 'Actualizado con éxito.');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage() . ' msg: ' . $th->getLine() . ' ' . $th->getFile(), 204);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CreditNoteType  $creditNoteType
     * @return \Illuminate\Http\Response
     */
    public function show(CreditNoteType $creditNoteType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CreditNoteType  $creditNoteType
     * @return \Illuminate\Http\Response
     */
    public function edit(CreditNoteType $creditNoteType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CreditNoteType  $creditNoteType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CreditNoteType $creditNoteType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CreditNoteType  $creditNoteType
     * @return \Illuminate\Http\Response
     */
    public function destroy(CreditNoteType $creditNoteType)
    {
        //
    }
}
