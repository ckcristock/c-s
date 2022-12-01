<?php

namespace App\Http\Controllers;

use App\Models\BonusPerson;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class BonusPersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BonusPerson  $bonusPerson
     * @return \Illuminate\Http\Response
     */
    public function show(BonusPerson $bonusPerson)
    {
        //
    }

    public function pdfGenerate($id, $period)
    {

        $data = BonusPerson::with('bonus', 'person')
                        ->where('person_id', $id)
                        ->where('lapse', $period)
                        ->first();

        //nota, se le agrega a cada uno para que sirva desde el coponente individual
        $anio = explode('-',$data->lapse)[0];
        $period = explode('-',$data->lapse)[1];
        $data->fecha_inicio = ($period==1) ? '01 Enero '.$anio : '01 Julio '.$anio ;

        $pdf = PDF::loadView('pdf.bonus_person', ['bonus'=>$data])
                    ->setPaper([0,0,614.295,397.485]);

        return $pdf->stream('colilla_prima.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BonusPerson  $bonusPerson
     * @return \Illuminate\Http\Response
     */
    public function edit(BonusPerson $bonusPerson)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BonusPerson  $bonusPerson
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BonusPerson $bonusPerson)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BonusPerson  $bonusPerson
     * @return \Illuminate\Http\Response
     */
    public function destroy(BonusPerson $bonusPerson)
    {
        //
    }
}
