<?php

namespace App\Http\Libs\Nomina\Calculos;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CalculoCesantias
{
    protected $cantidadHoras = [];
    protected $horasReportadas = [];
    private $horasTotales = [];
    private $valorTotal;
    private $datos;

    public function __construct($datos = [])
    {
        $this->datos = $datos;
    }


    public function calcularCesania()
    {
        /***
         * Ley: se incluyen todos los beneficios del trabajador, incluyendo comisiones, etc.
         * La fórmula es: salario(con todos los beneficios) [por]  X días trabajados en el año
         * [entre] 360 días del año.
         * Se incluyen los trabajadores que estén activos y con contrato igual o superior al 30 de diciembre
         * del año que recién se terminó.
         * Cada trabajador tiene su propio fondo de cesantías y a ese se consigna
         * En el controlador, ya la consulta incluye los datos del fondo de cesantías
         *
         * si el salario es fijo durante el año, se calcula en bae al último salario,
         * si ha tenido variaciones, se calcula en base al promedio
         * sin importar el tipo de contrato que tenga
         *
         */
        dd(self::$datos);
    }

     /**
     * Aplicar el contract de la interfaz, crear la colección
     *
     * @return Illuminate\Support\Collection
     */
    public function crearColeccion()
    {
        return new Collection([
            'funcionarios' => 'asfd'
        ]);
    }
}
