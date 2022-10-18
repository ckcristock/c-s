<?php

namespace App\Http\Controllers;

use App\Models\Company;
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
        $logo = public_path('app/public') . '/logo2.png';
        $work_certificate = WorkCertificate::find($id);
        $json = json_decode($work_certificate->information);
        $textoSalario = '';
        $textoCargo = '';
        $addressee = '';
        $gener = '';
        $formatterES = new NumberFormatter("es", NumberFormatter::SPELLOUT);
        $date = Carbon::now()->locale('es');
        $company = Company::find(1)->first();
        $funcionario = DB::table('people')
            ->find($work_certificate->person_id);

        $contract =
            WorkContract::with('work_contract_type')->where('person_id', '=', $funcionario->id)
            ->first();
        $position =
            DB::table('positions')
            ->where('id', '=', $contract->position_id)
            ->first();
        if ($work_certificate->addressee) {
            $addressee = $work_certificate->addressee;
        } else {
            $addressee = 'A QUIEN INTERESE';
        }
        if ($funcionario->gener == 'Masculino') {
            $gener = 'certifica que el señor';
        } else {
            $gener = 'certifica que la señora';
        }
        foreach ($json as $information) {
            if ($information == 'salario') {
                $textoSalario = ' y devengando un salario mensual de ' . $formatterES->format($contract->salary) . ' ($' .
                    number_format($contract->salary, 0, ".", ",") . ')';
            }
            if ($information == 'cargo') {
                $textoCargo = ' desempeñando el cargo de 
                <b>' . $position->name . "</b>";
            }
        }
        $contenido =
            '<!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">               
            </head>
            <body style="margin:2rem">
                <img src="' . $logo . '">
                
                <p style="font-size:16px;font-weight:bold;margin-top:100px">Girón, ' .
            CARBON::parse($date)->locale('es')->translatedFormat('l j F Y') . '</p>
                <p>' . $addressee . '</p>
                <p style="margin-top:30px; text-align: justify;">
                    La empresa ' . $company->social_reason . ' con ' . $company->document_type . ' ' . $company->document_number . '-'
            . $company->verification_digit . ', ' . $gener . ' 
                            <b>'
            . $funcionario->first_name . ' '
            . $funcionario->second_name . ' '
            . $funcionario->first_surname . ' '
            . $funcionario->second_surname .
            '</b> 
                    identificado(a) con cédula de ciudadanía No. 
                    <b>' . number_format($funcionario->identifier, 0, "", ".") . '</b> de ' . $funcionario->place_of_birth . ' laboró en la empresa
                    desde el ' . CARBON::parse($contract->date_of_admission)->locale('es')->isoFormat('DD MMMM YYYY') . ', con un contrato ' . $contract->work_contract_type->name .
            $textoCargo . $textoSalario . ', . La presente certificación se expide en Bucaramanga, al (los) ' . $date->format('d') . ' día(s) del mes de ' .
            $date->isoFormat('MMMM') . ' a solicitud del interesado. Y el motivo del retiro ' . $work_certificate->reason . '
                </p>
                <p style="margin-top:280px;">Atentamente;</p>

                <table style="margin-top:20px">   
                    <tr>
                        <td style="padding-top: 40px">
                            ____________________________________
                        </td>
                    </tr> 
                    <tr>                        
                        <td style="font-weight:bold; text-align:center;">
                            ALBERTO BALCARCEL <br>
                            Director administrativo
                        </td>   
                    </tr>
                </table>
            </body>
        </html>';
        //return $this->success($position);
        $pdf = PDF::loadHTML($contenido);
        return $pdf->download('certificado.pdf');
    }
}
