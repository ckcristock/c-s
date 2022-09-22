<?php

namespace App\Http\Controllers;

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
        LayoffsCertificate::find($id)
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
        $layoffs_certificate = LayoffsCertificate::find($id);
        $date = Carbon::now()->locale('es');
        $funcionario = DB::table('people')
            ->find($layoffs_certificate->person_id);            
        $compensation_fund = DB::table('compensation_funds')
            ->find($funcionario->compensation_fund_id);
        $motivo = DB::table('reason_withdrawal')
            ->find($layoffs_certificate->reason_withdrawal);
        $contenido = '<!DOCTYPE html>
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
                <p style="margin-top:150px;"><b>Bucaramanga, '.$date->isoFormat('D MMMM Y').'</b></p>
                <p><b>Señores</b></p>
                <p>'.strtoupper($compensation_fund->name).'</p>
                <p><b>ASUNTO:</b> '.strtoupper($motivo->name).'</p>
                <p>
                    Según lo dispuesto en el artículo 21 de la Ley 1429 de 2010 (que modificó el Art. 256 del código 
                    sustantivo del trabajo) y a la aclaración contenida en la Carta Circular 011 del 7 de Febrero de 2011 
                    del Ministerio de la Protección Social, nos permitimos informarles que hemos autorizado el retiro de 
                    cesantías por '.strtoupper($motivo->name).' señalado más adelante, en las siguientes condiciones:
                </p>
                <p>
                    <b>Nombres del Funcionario(a):</b> '
                    .strtoupper($funcionario->first_name . ' ' 
                    . $funcionario->second_name . ' ' 
                    . $funcionario->first_surname . ' ' 
                    . $funcionario->second_surname).'
                </p>
                <p><b>Identificación:</b> '.number_format($funcionario->identifier, 0, "", ".").'</p>
                <p><b>Concepto del Retiro:</b> '.strtoupper('????????').'</p>
                <p>La empresa se compromete a vigilar la inversión de las cesantías de acuerdo con lo estipulado en las normas antes señaladas.</p>
                <p>Cordialmente,</p>
                <table style="margin-top:50px">    
                    <tr>
                        <td style="width:400px;padding-left:10px">
                        <table>
                        <tr>
                            <td style="width:300px;font-weight:bold; border-top:1px solid black; text-align:center;">'.$funcionario->first_name . ' ' 
                            . $funcionario->second_name . ' ' 
                            . $funcionario->first_surname . ' ' 
                            . $funcionario->second_surname.'</td>
                        </tr>
                        <tr>
                            <td style="width:300px;font-weight:bold; text-align:center;">C.C '.number_format($funcionario->identifier,0,"",".").' </td>    
                        </tr>
                        
                        </table>
                        </td>    
                    </tr>
                </table>
            </body>
        </html>';
        $pdf = PDF::loadHTML($contenido);
        return $pdf->download('comprobante.pdf');
    }
}
