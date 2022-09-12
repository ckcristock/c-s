<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Models\WorkCertificate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;

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
                    $q->where('name', 'like', '%' . $fill . '%');
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
        $date = Carbon::now()->locale('es');
        $funcionario = DB::table('people')
            ->find($work_certificate->person_id);
        $contract =
            DB::table('work_contracts')
            ->where('person_id', '=', $funcionario->id)
            ->first();
        $position =
            DB::table('positions')
            ->where('id', '=', $contract->position_id)
            ->first();
        $contenido = 
        '<!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    #space-between {
                        display: flex;
                        justify-content: space-between;
                    }
                </style>
            </head>
            <body>
                <img src="' . $logo . '">
                <p style="text-align: center;font-size:22px;font-weight:bold;margin-top:350px">CERTIFICADO LABORAL</p>
                <p style="margin-top:30px;">
                    Certificamos que el(la) señor(a) 
                    <b>'
                    .$funcionario->first_name . ' ' 
                    . $funcionario->second_name . ' ' 
                    . $funcionario->first_surname . ' ' 
                    . $funcionario->second_surname .
                    '</b> 
                    identificado(a) con cédula de ciudadanía No. 
                    <b>'.number_format($funcionario->identifier,0,"",".").'</b> 
                    se encuentra vinculado(a) a la empresa MAQMO NIT. 8002265011. Desempeñando el cargo de 
                    <b>'.$position->name."</b>". ' y devengando un salario mensual de '. 
                    number_format($contract->salary, 2, ".", ",") .' , desde el '.$contract->date_of_admission.
                    '. La presente certificación se expide en Bucaramanga, al(los) '.$date->format('d').' día(s) del mes de '.
                    $date->isoFormat('MMMM').' a solicitud del interesado.
                </p>
                <p style="margin-top:250px;">Atentamente;</p>

                <table style="margin-top:20px">    
                    <tr>
                        <td style="width:300px;padding-left:10px">
                            Aquí la firma
                        <table>
                        <tr>
                            <td style="width:300px;font-weight:bold; border-top:1px solid black; text-align:center;">
                            Nombre jefe de recursos humanos</td>
                        </tr>
                        <tr>
                            <td style="width:300px;font-weight:bold; text-align:center;">Jefe de recursos humanos </td>    
                        </tr>
                        
                        </table>
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
