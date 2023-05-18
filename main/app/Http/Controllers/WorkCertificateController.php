<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Person;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Models\WorkCertificate;
use App\Models\WorkContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;
use NumberFormatter;

class WorkCertificateController extends Controller
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
            DB::table('work_certificates')->when(
                Request()->get('name'),
                function ($q, $fill) {
                    $q->where('person_id', 'like', '%' . $fill . '%');
                }
            )
                ->join('people', 'people.id', '=', 'work_certificates.person_id')
                ->select('work_certificates.*', 'people.first_name', 'people.first_surname', 'people.image')
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
        if (WorkCertificate::where('person_id', $request->get('person_id'))
            ->whereMonth('created_at', $now->month)->exists()
        ) {
            return $this->error('Hemos encontrado un certificado laboral solicitado en este mes para este usuario', 409);
        } else {
            $json = json_encode($request->information, true);
            $request->merge(['information' => $json]);
            WorkCertificate::create($request->all());
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



    public function pdf($id)
    {
        $date = Carbon::now()->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');
        $work_certificate = WorkCertificate::where('id', $id)->first();
        $informations = json_decode($work_certificate->information);
        $formatterES = new NumberFormatter("es", NumberFormatter::SPELLOUT);
        $date2 = Carbon::now();
        $company = Company::first();
        $funcionario = Person::where('id', $work_certificate->person_id)
            ->with('contractultimate')->name()->first();
        $date_of_admission = Carbon::parse($funcionario->contractultimate->date_of_admission)->locale('es')->isoFormat('DD MMMM YYYY');
        $salario_numeros = $formatterES->format($funcionario->contractultimate->salary);
        $addressee = $work_certificate->addressee ?: 'A QUIEN INTERESE';
        $gener = $funcionario->gener === 'Masculino' ? 'certifica que el señor' : 'certifica que la señora';
        $image = $company->page_heading;
        $datosCabecera = (object) array(
            'Titulo' => 'Certificación laboral',
            'Codigo' => $work_certificate->code,
            'Fecha' => $work_certificate->created_at,
            'CodigoFormato' => $work_certificate->format_code
        );
        $pdf = PDF::loadView('pdf.certificado_laboral', [
            'date' => $date,
            'date2' => $date2,
            'company' => $company,
            'gener' => $gener,
            'funcionario' => $funcionario,
            'date_of_admission' => $date_of_admission,
            'work_certificate' => $work_certificate,
            'informations' => $informations,
            'salario_numeros' => $salario_numeros,
            'addressee' => $addressee,
            'datosCabecera' => $datosCabecera,
            'image' => $image
        ]);
        return $pdf->download('certificado.pdf');
    }
}
