<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class EstadoResultadoExport implements FromView, ShouldAutoSize
{

    private $contenido ;
    public function __construct($contenido)
    {
        $this->contenido = $contenido;
    }

    public function view(): View
    {
        return view('exports.EstadoResultado', ['contenido'=>$this->contenido]);
    }
}
