<style>
    .page-content{
    width:750px;
    }
    .row{
    display:inlinie-block;
    width:750px;
    }
    .td-header{
        font-size:15px;
        line-height: 20px;
    }
    .titular{
        font-size: 11px;
        text-transform: uppercase;
        margin-bottom: 0;
    }
</style>
     <h4 style="margin:5px 0 0 0;font-size:18px;line-height:22px;">AMORTIZACIÓN PRESTAMO</h4>
     <h5 style="margin:5px 0 0 0;font-size:16px;line-height:16px;">'.fecha($data["Fecha"]).'</h5>
    <table style="background: #e6e6e6;">
        <tr style=" min-height: 200px; background: #e6e6e6;padding: 15px; border-radius: 10px; margin: 0;">
       
        <td style="font-size:11px;font-weight:bold;width:200px;padding:5px">
        Identificación Empleado:
        </td>

        <td style="font-size:11px;width:510px;padding:5px">
        '.number_format($data['Identificacion_Funcionario'],0,"",".").'
        </td>
        
    </tr>
    
    <tr style=" min-height: 200px; background: #e6e6e6; padding: 15px; border-radius: 10px; margin: 0;">
        <td style="font-size:11px;font-weight:bold;width:200px;padding:5px">
        Nombre Empleado:
        </td>
        <td style="font-size:11px;width:510px;padding:5px">
        '.$funcionario['Nombres'].' '.$funcionario['Apellidos'].'
        </td>
    </tr>
    <tr style=" min-height: 200px; background: #e6e6e6; padding: 15px; border-radius: 10px; margin: 0;">
        <td style="font-size:11px;font-weight:bold;width:200px;padding:5px">
        Valor Prestamo:
        </td>
        <td style="font-size:11px;width:510px;padding:5px">
        $ '.number_format($data['Valor_Prestamo'],2,",",".").'
        </td>
    </tr>
    <tr style=" min-height: 200px; background: #e6e6e6; padding: 15px; border-radius: 10px; margin: 0;">
        <td style="font-size:11px;font-weight:bold;width:200px;padding:5px">
        Interes:
        </td>
        <td style="font-size:11px;width:510px;padding:5px">
        '.number_format($data['Intereses'],2,",",".").'%
        </td>
    </tr>
    <tr style=" min-height: 200px; background: #e6e6e6; padding: 15px; border-radius: 10px; margin: 0;">
        <td style="font-size:11px;font-weight:bold;width:200px;padding:5px">
        Cuotas:
        </td>
        <td style="font-size:11px;width:510px;padding:5px">
        '.$data['Nro_Cuotas'].'
        </td>
    </tr>
    <tr style=" min-height: 200px; background: #e6e6e6; padding: 15px; border-radius: 10px; margin: 0;">
        <td style="font-size:11px;font-weight:bold;width:200px;padding:5px">
        Valor Cuota:
        </td>
        <td style="font-size:11px;width:510px;padding:5px">
        $ '.number_format($data['Cuota_Mensual'],2,",",".").'
        </td>
    </tr>
</table>
<table style="font-size:10px;margin-top:10px;" cellpadding="0" cellspacing="0">
    <tr>
        <td style="width:60px;max-width:60px;font-weight:bold;background:#cecece;;border:1px solid #cccccc;">
            Cuota
        </td>
        <td style="width:150px;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
           Fecha Descuento
        </td>
        <td style="width:140px;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
            Amortización
        </td>
        <td style="width:120px;max-width:120px;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
            Intereses
        </td>
        <td style="width:120px;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
            Total Cuota
        </td>
        <td style="width:120px;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
            Saldo
        </td>
    </tr>

    foreach ($proyecciones['Proyeccion'] as $i => $value) {

    $contenido .= '<tr>
    <td style="vertical-align:center;font-size:9px;width:50px;max-width:50px;text-align:center;border:1px solid #cccccc;">
        '.($i+1).'
    </td>
    <td style="vertical-align:center;text-align:center;font-size:9px;width:90px;border:1px solid #cccccc;">
        '.$value['Fecha'].'
    </td>
    <td style="vertical-align:center;text-align:right;font-size:9px;word-break:break-all;width:60px;max-width:60px;border:1px solid #cccccc;">
        $ '.number_format($value['Amortizacion'],2,",",".").'
    </td>
    <td style="width:100px;max-width:100px;text-align:right;font-size:9px;word-break:break-all;border:1px solid #cccccc;">
        $ '.number_format($value['Intereses'],2,",",".").'
    </td>
    <td style="vertical-align:center;text-align:right;font-size:9px;text-align:right;width:75px;border:1px solid #cccccc;">
        $ '.number_format($value['Valor_Cuota'],2,'.',',').'
    </td>
    <td style="vertical-align:center;text-align:right;font-size:9px;text-align:right;width:75px;border:1px solid #cccccc;">
        $ '.number_format($value['Saldo'],2,'.',',').'
    </td>
</tr>';

}


<tr>
    <td colspan="2" style="padding:4px;text-align:right;border:1px solid #cccccc;font-weight:bold;font-size:12px">TOTALES:</td>
    <td style="padding:4px;text-align:right;border:1px solid #cccccc;">
        $ '.number_format(getTotales($proyecciones['Proyeccion'], 'Amortizacion'),2,".",",").'
    </td>
    <td style="padding:4px;text-align:right;border:1px solid #cccccc;">
        $ '.number_format(getTotales($proyecciones['Proyeccion'], 'Intereses'),2,".",",").'
    </td>
    <td style="padding:4px;text-align:right;border:1px solid #cccccc;">
        $ '.number_format(getTotales($proyecciones['Proyeccion'], 'Valor_Cuota'),2,".",",").'
    </td>
    <td style="padding:4px;text-align:right;border:1px solid #cccccc;"></td>
</tr>
</table>
<p style="margin-top:10px;">Atentamente;</p>
<table style="margin-top:50px">    
    <tr>
        <td style="width:400px;padding-left:10px">
            <table>
                <tr>
                    <td style="width:300px;font-weight:bold; border-top:1px solid black; text-align:center;">'.$funcionario['Nombres']." ".$funcionario["Apellidos"].'</td>
                    <td style="width:30px;"></td>
                    <td style="width:300px;font-weight:bold; border-top:1px solid black; text-align:center;">'.$config["Representante_Legal"].'</td>
                </tr>
                <tr>
                    <td style="width:300px;font-weight:bold; text-align:center;">C.C. '.number_format($funcionario['Identificacion_Funcionario'],0,",",".").' </td>    
                    <td style="width:30px;"></td>    
                    <td style="width:300px;font-weight:bold; text-align:center;">Representante Legal</td>    
                </tr>
            </table>
        </td>    
    </tr>
</table>