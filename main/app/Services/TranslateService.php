<?php

namespace App\Services;

class TranslateService
{
    public function translateDay($day)
    {
        switch ($day) {
            case 'Monday':
                return 'Lunes';
                break;
            case 'Tuesday':
                return 'Martes';
                break;
            case 'Wednesday':
                return 'Miercoles';
                break;
            case 'Thursday':
                return 'Jueves';
                break;
            case 'Friday':
                return 'Viernes';
                break;
            case 'Saturday':
                return 'Sabado';
                break;
            case 'Sunday':
                return 'Domingo';
                break;
            default:
                break;
        }
    }

    public function traslateIntDay($nro)
    {
        switch ($nro) {
            case 1:
                return 'Lunes';
                break;
            case 2:
                return 'Martes';
                break;
            case 3:
                return 'Miércoles';
                break;
            case 4:
                return 'Jueves';
                break;
            case 5:
                return 'Viernes';
                break;
            case 6:
                return 'Sabado';
                break;
            case 0:
                return 'Dommingo';
                break;
            default:
                break;
        }
    }

    public function translateMonth($month)
    {
        switch ($month) {
            case 1:
                return 'Enero';
                break;
            case 2:
                return 'Febrero';
                break;
            case 3:
                return 'Marzo';
                break;
            case 4:
                return 'Abril';
                break;
            case 5:
                return 'Mayo';
                break;
            case 6:
                return 'Junio';
                break;
            case 7:
                return 'Julio';
                break;
            case 8:
                return 'Agosto';
                break;
            case 9:
                return 'Septiembre';
                break;
            case 10:
                return 'Octubre';
                break;
            case 11:
                return 'Noviembre';
                break;
            case 12:
                return 'Diciembre';
                break;
            default:
                break;
        }
    }
}
