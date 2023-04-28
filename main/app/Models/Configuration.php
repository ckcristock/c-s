<?php

namespace App\Models;

use Egulias\EmailValidator\Exception\ConsecutiveAt;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PhpParser\ErrorHandler\Collecting;

class Configuration extends Model
{

    protected $table = 'Configuracion';
    protected $primaryKey = 'Id_Configuracion';
    public $timestamps = false;

    protected $fillable = [
        'PagoNomina',
        'Extras_Legales',
        'Festivos_Legales',
        'Llegadas_Tarde',
        'Salario_Base',
        'Subsidio_Transporte',
        'Maximo_Cotizacion',
        'Hora_Inicio_Dia',
        'Hora_Fin_Dia',
        'Hora_Extra_Diurna',
        'Hora_Extra_Nocturna',
        'Hora_Extra_Domingo_Diurna',
        'Hora_Extra_Domingo_Nocturna',
        'Recargo_Dominical_Diurna',
        'Recargo_Dominical_Nocturna',
        'Recargo_Nocturno',
        'Hora_Inicio_Noche',
        'Hora_Fin_Noche',
        'Festivos',
        'Libres',
        'Nombre_Empresa',
        'NIT',
        'Telefono',
        'Celular',
        'Correo',
        'Direccion',
        'Condiciones_Comerciales',
        'Numero_Resolucion',
        'Fecha_Resolucion',
        'Numero_Inicio_Facturacion',
        'Numero_Fin_Facturacion',
        'Consecutivo',
        'Codigo_Formato_Cotizacion',
        'Codigo_Formato_Devolucion_Compras',
        'Codigo_Formato_Devolucion_Comprobante_Egreso',
        'Codigo_Formato_Devolucion_Comprobante_Ingreso',
        'Codigo_Formato_Devolucion_Nota_Credito',
        'Codigo_Formato_Devolucion_Nota_Debito',
        'Codigo_Formato_Devolucion_Remision',
        'Codigo_Formato_Devolucion_Ventas',
        'Codigo_Formato_Homologos',
        'Codigo_Formato_OC',
        'Codigo_Formato_Remision',
        'Codigo_Formato_Traslados',
        'Comprobante_Egreso',
        'Comprobante_Ingreso',
        'Cotizacion',
        'Devolucion_Compras',
        'Devolucion_Remision',
        'Devolucion_Ventas',
        'Homologo',
        'Nombre_Dian_Cotizacion',
        'Nombre_Dian_Devolucion_Compras',
        'Nombre_Dian_Devolucion_Comprobante_Egreso',
        'Nombre_Dian_Devolucion_Comprobante_Ingreso',
        'Nombre_Dian_Devolucion_Nota_Credito',
        'Nombre_Dian_Devolucion_Nota_Debito',
        'Nombre_Dian_Devolucion_Remision',
        'Nombre_Dian_Devolucion_Ventas',
        'Nombre_Dian_Homologos',
        'Nombre_Dian_OC',
        'Nombre_Dian_Remision',
        'Nombre_Dian_Traslados',
        'Nota_Credito',
        'Nota_Debito',
        'Orden_Compra',
        'Prefijo_Cotizacion',
        'Prefijo_Devolucion_Compras',
        'Prefijo_Devolucion_Comprobante_Egreso',
        'Prefijo_Devolucion_Comprobante_Ingreso',
        'Prefijo_Devolucion_Nota_Credito',
        'Prefijo_Devolucion_Nota_Debito',
        'Prefijo_Devolucion_Remision',
        'Prefijo_Devolucion_Ventas',
        'Prefijo_Homologo',
        'Prefijo_Orden_Compra',
        'Prefijo_Remision',
        'Prefijo_Traslados',
        'Remision',
        'Traslado',
        'Logo_Color',
        'Logo_Negro',
        'Logo_Blanco',
        'Resolucion',
        'Nota_1',
        'Nota_2',
        'Cuenta_Bancaria',
        'Porcentaje_Rotativo',
        'Dias_Capita',
        'Dias_Otros_Servicios',
        'Prefijo_No_Conforme',
        'Orden_No_Conforme',
        'Codigo_Formato_No_Conforme',
        'No_Conforme',
        'Acta_Recepcion',
        'Capita',
        'Nombre_Dian_Capita',
        'Prefijo_Capita',
        'Codigo_Capita',
        'Tolerancia_Peso_Global',
        'Tolerancia_Peso_Individual',
        'Prefijo_Acta_Recepcion',
        'Prefijo_Acta_Recepcion_Remision',
        'Acta_Recepcion_Remision',
        'Prefijo_Remision_Antigua',
        'Remision_Antigua',
        'Prefijo_Factura_Venta',
        'Factura_Venta',
        'Funcionarios_Autorizados_Inventario',
        'Prefijo_Ajuste_Individual',
        'Ajuste_Individual',
        'Cierre_Caja',
        'Prefijo_Cierre_Caja',
        'Rotativo',
        'Prefijo_Nota_Credito',
        'Prefijo_Comprobante_Ingreso',
        'Prefijo_Comprobante_Egreso',
        'Prefijo_Nota_Contable',
        'Nota_Contable',
        'Meses_Vencimiento',
        'Productos_Pendientes_Libres',
        'Radicacion',
        'Prefijo_Radicacion',
        'Representante_Legal',
        'Identificacion_Representante',
        'Id_Departamento',
        'Id_Municipio',
        'Valor_Unidad_Tributaria',
        'Base_Retencion_Compras_Reg_Comun',
        'Base_Retencion_Compras_Reg_Simpl',
        'Base_Retencion_Compras_Ica',
        'Base_Retencion_Iva_Reg_Comun',
        'Cantidad_Formulada',
        'Ley_1607',
        'Ley_1429',
        'Ley_590',
        'Salarios_Minimo_Cobro_Incapacidad',
        'Orden_Compra_Internacional',
        'Prefijo_Orden_Compra_Internacional',
        'Salario_Auxilio_Transporte',
        'Valor_Uvt',
        'Salarios_Minimos_Cobro_Seguridad_Social',
        'Nit_Sena',
        'ICBF',
        'Id_Arl',
        'Prefijo_Acta_Recepcion_Internacional',
        'Acta_Recepcion_Internacional',
        'Prefijo_No_Conforme_Internacional',
        'No_Conforme_Internacional',
        'Prefijo_Parcial_Acta_Internacional',
        'Parcial_Acta_Internacional',
        'Max_Item_Remision',
        'Prefijo_Gasto_Punto',
        'Gasto_Punto',
        'Max_Costo_Nopos',
        'Codigo_Sede',
        'Prefijo_Devolucion_Interna',
        'Devolucion_Interna',
        'Nota_Credito_Global',
        'Prefijo_Nota_Credito_Global',
        'Prefijo_Rtica_Certificados',
        'Prefijo_Prestamo',
        'Prestamo',
        'Responsable_Rh',
        'Responsable_Apro_Nomina_Contabilidad',
        'Responsable_Apro_Nomina',
        'Responsable_Apro_Revisor',
        'Balanza',

    ];

    public function responsableRecursosHumanos()
    {
        return $this->hasOne(Funcionario::class, 'Identificacion_Funcionario', 'Responsable_Rh');
    }

    function prefijoConsecutivo($index)
    {
        $prop = "Prefijo_$index";
        return  $this->$prop;
    }

    function guardarConsecutivoConfig($index, $consecutivo)
    {

        $this->$index = $consecutivo += 1;
        $this->save();
    }

    function getConsecutivo($mod, $tipo_consecutivo)
    {
        // sleep(strval( rand(2, 8)));
        $query = "SELECT MAX(N.Codigo) AS Codigo FROM ( SELECT Codigo FROM $mod ORDER BY Id_$mod DESC LIMIT 10 )N ";
        $res = DB::select($query);
        $res = $res[0];


        $prefijo = $this->prefijoConsecutivo($tipo_consecutivo);
        $NumeroCodigo = substr($res->Codigo, strlen($prefijo));

        $NumeroCodigo += 1;

        $cod = $prefijo . $NumeroCodigo;

        $query = "SELECT Id_$mod AS ID FROM $mod WHERE Codigo = '$cod'";

        $res2 = DB::select($query);

        $res2 = $res2 ? $res2[0] : $res2;
        if ($res2) {
            sleep(strval(rand(0, 3)));
            $this->getConsecutivo($mod, $tipo_consecutivo);
        }

        $this->guardarConsecutivoConfig($tipo_consecutivo, $NumeroCodigo);

        return $cod;
    }

    function getConsecutivoPro($mod, $tipo_consecutivo)
    {
        // sleep(strval( rand(2, 8)));
        $query = "SELECT MAX(N.code) AS code FROM ( SELECT code FROM $mod ORDER BY Id_$mod DESC LIMIT 10 )N ";
        $res = DB::select($query);
        $res = $res[0];


        $prefijo = $this->prefijoConsecutivo($tipo_consecutivo);
        $NumeroCodigo = substr($res->code, strlen($prefijo));

        $NumeroCodigo += 1;

        $cod = $prefijo . $NumeroCodigo;

        $query = "SELECT Id_$mod AS ID FROM $mod WHERE code = '$cod'";

        $res2 = DB::select($query);

        $res2 = $res2 ? $res2[0] : $res2;
        if ($res2) {
            sleep(strval(rand(0, 3)));
            $this->getConsecutivo($mod, $tipo_consecutivo);
        }

        $this->guardarConsecutivoConfig($tipo_consecutivo, $NumeroCodigo);

        return $cod;
    }

    function Consecutivo($index)
    {
        $num_cotizacion = $this->$index;

        $consecutivo = number_format((int) $this->$index, 0, "", "");
        $this->$index = $consecutivo + 1;
        $this->save();

        $d = "Prefijo_" . $index;
        $cod = $this->$d;
        $cod . sprintf("%05d", $num_cotizacion);

        return $cod;
    }


    function consecutivoLevel1($tipo_consecutivo)
    {

        $prefix = 'Prefijo_' . $tipo_consecutivo;
        $numberPos = 'Consecutivo_' . $tipo_consecutivo;

        $data = new Collection();
        $data->prefix = $this->$prefix;
        $data->number = $this->$numberPos + 1;
        $data->code = $this->$prefix . ($this->$numberPos + 1);

        return  $data;
    }

    function savePrefix($tipo_consecutivo)
    {
        $numberPos = 'Consecutivo_' . $tipo_consecutivo;
        $this->$numberPos = $this->$numberPos + 1;
        $this->save();
    }
}
