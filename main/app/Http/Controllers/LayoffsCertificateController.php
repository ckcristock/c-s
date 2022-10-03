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
        $logo = public_path('app/public') . '/logo2.png';
        $layoffs_certificate = LayoffsCertificate::find($id);
        $date = Carbon::now()->locale('es');
        $funcionario = DB::table('people')
            ->find($layoffs_certificate->person_id);   
        $compensation_fund = DB::table('severance_funds')
            ->find($funcionario->severance_fund_id);
        $motivo = DB::table('reason_withdrawal')
            ->find($layoffs_certificate->reason_withdrawal);
        if ($layoffs_certificate->monto == 'parcial') {
            $asunto = 'RETIRO PARCIAL DE CESANTIAS';
            $p1 = 'Respetados señores: <br><br>
                Para efectos de lo dispuesto en el decreto 2076 de 1967 y normas concordantes, 
                nos permitimos solicitar a ustedes que se sirvan autorizar el pago de la referencia 
                a favor del siguiente empleado.';
            $valor = '
            <tr>
                <td><b>VALOR CESANTIAS SOLICITADO:</b></td>
                <td>$' . number_format($layoffs_certificate->valormonto, 0, "", ".") .'</td>
            </tr>';
            $motivotext = '
            <tr>
                <td><b>INVERSION O DESTINO:</b></td>
                <td>' . strtoupper($motivo->name) .'</td>
            </tr>';
            $p2 = 'Lo anterior previa presentación de los soportes presentados para tal fin de conformidad con lo establecido en la ley 1429 
                artículo 21 de 2010, por lo anterior, confirmamos el retiro en mención al trabajador, quien va a utilizar los recursos de sus 
                cesantías conforme a las condiciones previstas en la ley. <br><br>
                MAQUINADOS Y MONTAJES S.A.S, Se compromete a vigilar la inversión conforme a lo dispuesto por el decreto antes citado y según 
                resolución No 04250 de 1973, en concordancia con la circular N°000 de 1974.';
        } else if ($layoffs_certificate->monto == 'total') {
            $asunto = 'Autorización retiro total de Cesantías';
            $p1 = '';
            $valor = null;
            $motivotext = '
            <tr>
                <td><b>MOTIVO:</b></td>
                <td>' . strtoupper($motivo->name) .'</td>
            </tr>';
            $p2 = null;
        }
        
            
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
                    html * {
                        color: #000 !important;
                        font-family: Arial !important;
                    }
                </style>
            </head>
            <body style="margin: 2rem"; width: 700px>
                <img style="float: right;" src="' . $logo . '">
                <p style="margin-top:15px;"><b>Girón, '.$date->isoFormat('D MMMM Y').'</b></p>
                <div style="margin-top:80px;"><b>Señores</b></div>
                <div>'.strtoupper($compensation_fund->name).'</div>
                <p style="margin-top:60px;"><b>ASUNTO:</b> '.strtoupper($asunto).'</p>
                <p style="text-align: justify;">'. $p1 . '</p>
                <table style="margin-top: 80px;">
                    <tbody>
                        <tr>
                            <td style="width:50%;"><b>NOMBRE DEL EMPLEADO(A):</b></td>
                            <td>'.strtoupper($funcionario->first_name . ' ' 
                                . $funcionario->second_name . ' ' 
                                . $funcionario->first_surname . ' ' 
                                . $funcionario->second_surname).'
                            </td>
                        </tr>
                        <tr>
                            <td><b>IDENTIFICACION:</b></td>
                            <td>'.number_format($funcionario->identifier, 0, "", ".").'</td>
                        </tr>
                        ' . $valor . $motivotext . '
                    </tbody>
                </table>
                <p style="margin-top:30px; text-align: justify">' . $p2 . '</p>
                <table style="margin-top:50px">    
                    <tr>
                        <td>Cordialmente,</td>
                        <td>Trabajador,</td>
                    </tr>
                    <tr>
                        <td style="padding-top: 40px">
                            ____________________________________
                        </td>
                        <td style="padding-top: 40px">
                            ____________________________________
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">ALBERTO BALCARCEL ZAMBRANO</td>
                        <td style="font-weight:bold;">'.$funcionario->first_name . ' ' 
                            . $funcionario->second_name . ' ' 
                            . $funcionario->first_surname . ' ' 
                            . $funcionario->second_surname.'
                        </td>
                    </tr>
                    <tr>
                        <td>Representante legal</td>
                        <td>C.C '.number_format($funcionario->identifier,0,"",".").' </td>    
                    </tr>                        
                </table>
            </body>
        </html>';
        $pdf = PDF::loadHTML($contenido);
        return $pdf->download('comprobante.pdf');
    }
}
