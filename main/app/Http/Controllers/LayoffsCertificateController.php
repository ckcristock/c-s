<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Models\LayoffsCertificate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\URL;

class LayoffsCertificateController extends Controller
{
    use ApiResponser;
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
    public function paginate()
    {
        return $this->success(
            DB::table('layoffs_certificates')->when(
                Request()->get('name'),
                function ($q, $fill) {
                    $q->where('person_id', 'like', '%' . $fill . '%');
                }
            )
                ->join('people', 'people.id', '=', 'layoffs_certificates.person_id')
                ->select('layoffs_certificates.*', 'people.first_name', 'people.first_surname', 'people.image')
                ->orderByDesc('created_at')
                ->paginate(Request()->get('pageSize', 5), ['*'], 'page', Request()->get('page', 1))
        );
    }

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

        $now = Carbon::now();
        if (LayoffsCertificate::where('person_id', $request->get('person_id'))
            ->whereMonth('created_at', $now->month)->exists()
        ) {
            return $this->error('Hemos encontrado una solicitud de cesantías hecha en este mes para este usuario', 409);
        } else {
            $base64 = saveBase64File($request["document"], 'cesantias/', false, '.pdf');
            $file = URL::to('/') . '/api/file?path=' . $base64;
            LayoffsCertificate::create([
                'reason_withdrawal' => $request->reason_withdrawal,
                'person_id' => $request->person_id,
                'reason' => $request->reason,
                'document' => $file,
                'monto' => $request->monto,
                'valormonto' => $request->valormonto,
            ]);
            return $this->success('correcto');
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
        //
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
        LayoffsCertificate::where('id', $id)
            ->update(['state' => $request->state]);
        return $this->success('Actualizado con éxito');
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

    public function pdf($id)
    {
        $company = Company::first();
        $date = Carbon::now()->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');
        $layoffs_certificate = LayoffsCertificate::where('id', $id)
            ->with('person', 'reason_withdrawal_list')->first();
        $pdf = PDF::loadView('pdf.certificado_cesantias', [
            'date' => $date,
            'company' => $company,
            'layoffs_certificate' => $layoffs_certificate
        ]);
        return $pdf->download('comprobante.pdf');
    }
}
