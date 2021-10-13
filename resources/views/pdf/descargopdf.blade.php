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
    <table>
    <tbody>
      <tr>
        <td style="">
        </td>
        <td class="td-header" style="font-family:'Roboto', sans-serif;">
            <img src="{{public_path('/assets/img/logo.png')}}" style="width:200px;"/>
            {{ $company->company_name }}<br> 
            N.I.T.: {{$company->nit}}<br> 
            Correo: {{ $company->email_contact }}<br> 
            TEL: {{ $company->phone }}
        </td>
        <td style="width: 280px;">  </td>
        <td>
            <span>
                <p style="font-size: 17px">
                    Descargo <br>
                    D{{$funcionario->descargo_id}} <br>
                    {{ $company->fecha }}
                </p>
            </span>
        </td>
        <td>
            <img src="{{public_path('/assets/img/sinqr.png')}}"  style="width: 130px; max-width:100% margin-top:-10px;"/>
        </td>
      </tr>
    </tbody>
  </table>
    <table cellspacing="0" cellpadding="0" style="text-transform:uppercase;margin-top:20px;">
        <tr>
            <td style="font-size:10px;width:80px;background:#f3f3f3;vertical-align:middle;padding:3px;">
                <strong>Funcionario:</strong>
            </td>
            <td style="font-size:10px;width:460px;background:#f3f3f3;vertical-align:middle;padding:3px;">
                {{$funcionario->first_name." ".$funcionario->second_name." ".$funcionario->first_surname." ".$funcionario->second_surname}}
            </td>
            <td style="font-size:10px;width:50px;background:#f3f3f3;vertical-align:middle;padding:3px;">
                <strong>C.C.:</strong>
            </td>
            <td style="font-size:10px;width:50px;background:#f3f3f3;vertical-align:middle;padding:3px;">
                {{$funcionario->identifier}}
            </td>
        </tr>
    </table>
    <hr style="border:1px dotted #ccc;margin-right:35px;">
<table style="margin-top:50px">    
    <tr>
        <td style="width:400px;padding-left:10px">
            <table>
                <tr>
                    <td style="width:300px;font-weight:bold;font-family:'Roboto', sans-serif; border-top:1px solid black; text-align:center;">{{$funcionario->first_name." ".$funcionario->first_surname}}</td>
                    <td style="width:30px;"></td>
                    <td style="width:300px;font-weight:bold;font-family:'Roboto', sans-serif; border-top:1px solid black; text-align:center;"></td>
                </tr>
                <tr>
                    <td style="width:300px;font-weight:bold;font-family:'Roboto', sans-serif; text-align:center;">C.C. {{number_format($funcionario->identifier,0,",",".")}} </td>    
                    <td style="width:30px;"></td>    
                    <td style="width:300px;font-weight:bold;font-family:'Roboto', sans-serif; text-align:center;">Representante Legal</td>    
                </tr>
            </table>
        </td>    
    </tr>
</table>